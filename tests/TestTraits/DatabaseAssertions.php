<?php

namespace LaravelMultiNotify\Tests\TestTraits;

trait DatabaseAssertions {
    /**
     * Assert that a log entry exists in the database
     */
    protected function assertLogExists(array $attributes)
    {
        $baseQuery = \LaravelMultiNotify\Models\NotificationLog::query();
        $normalizedAttributes = $attributes;

        // Convert JSON strings in attributes to arrays
        foreach ($attributes as $key => $value) {
            if (in_array($key, ['content', 'response'])) {
                $normalizedAttributes[$key] = json_decode($value, true) ?? $value;
            }
        }

        // Filter non-JSON fields
        foreach ($normalizedAttributes as $key => $value) {
            if (!in_array($key, ['content', 'response'])) {
                $baseQuery->where($key, $value);
            }
        }

        $records = $baseQuery->get();
        $found = false;

        foreach ($records as $record) {
            $recordMatches = true;

            foreach ($normalizedAttributes as $key => $expectedValue) {
                if (!in_array($key, ['content', 'response'])) {
                    continue;
                }

                $actualValue = $record->$key;

                // Convert both values to arrays for comparison
                $normalizedExpected = is_string($expectedValue) ? json_decode($expectedValue, true) ?? $expectedValue : $expectedValue;
                $normalizedActual = is_string($actualValue) ? json_decode($actualValue, true) ?? $actualValue : $actualValue;

                if (!$this->areArraysEqual($normalizedExpected, $normalizedActual)) {
                    $recordMatches = false;
                    break;
                }
            }

            if ($recordMatches) {
                $found = true;
                break;
            }
        }

        // If no match found, show a helpful error message
        if (!$found) {
            $message = sprintf(
                "Failed asserting that a notification log matching %s exists.\nFound records: %s",
                json_encode($attributes, JSON_PRETTY_PRINT),
                $records->isEmpty() ? '[]' : $records->toJson(JSON_PRETTY_PRINT)
            );
            $this->assertTrue(false, $message);
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Compare two values for strict equality
     */
    private function areArraysEqual($expected, $actual)
    {
        // Handle null values
        if ($expected === null && $actual === null) {
            return true;
        }

        // Check string equality first
        if (is_string($expected) && is_string($actual)) {
            // Try to decode both strings
            $decodedExpected = json_decode($expected, true);
            $decodedActual = json_decode($actual, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedExpected)) {
                $expected = $decodedExpected;
            }
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedActual)) {
                $actual = $decodedActual;
            }

            if (!is_array($expected) && !is_array($actual)) {
                return trim($expected) === trim($actual);
            }
        }

        // Exit early if one is array but the other isn't
        if (is_array($expected) !== is_array($actual)) {
            return false;
        }

        // If neither is an array, compare directly
        if (!is_array($expected)) {
            return $expected === $actual;
        }

        // Sort and convert arrays to JSON for comparison
        $normalizedExpected = $this->normalizeAndSortArray($expected);
        $normalizedActual = $this->normalizeAndSortArray($actual);

        return $normalizedExpected === $normalizedActual;
    }

    /**
     * Sort array recursively and normalize values
     */
    private function normalizeAndSortArray($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        array_walk_recursive($array, function (&$value) {
            if (is_string($value)) {
                // Convert any string that looks like JSON to an array
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }
        });

        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->normalizeAndSortArray($value);
            }
        }

        ksort($array);
        return $array;
    }
}

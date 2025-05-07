<?php

namespace LaravelMultiNotify\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelMultiNotify\Models\NotificationLog;
use LaravelMultiNotify\Contracts\NotificationGateway;

abstract class BaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The recipient(s) of the notification
     *
     * @var string|array
     */
    public $to;

    /**
     * The notification data
     *
     * @var array
     */
    public $data;

    /**
     * The gateway to use
     *
     * @var string|null
     */
    public $gateway;

    /**
     * The notification channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Create a new job instance.
     *
     * @param string|array $to
     * @param array $data
     * @param string|null $gateway
     */
    public function __construct($to, array $data, ?string $gateway = null)
    {
        $this->to = $to;
        $this->data = $data;
        $this->gateway = $gateway;
        $this->setLogStatus('queued'); // Set initial status to queued
    }

    /**
     * Get the gateway instance
     * 
     * @return NotificationGateway
     */
    abstract protected function getGatewayInstance(): NotificationGateway;

    /**
     * Set the status in the notification log
     *
     * @param string $status
     * @param string|null $error
     * @param mixed|null $response
     * @return void
     */
    protected function setLogStatus(string $status, ?string $error = null, $response = null): void
    {
        if (is_array($this->to)) {
            foreach ($this->to as $recipient) {
                $this->createLog($recipient, $status, $error, $response);
            }
        } else {
            $this->createLog($this->to, $status, $error, $response);
        }
    }

    /**
     * Create a notification log entry
     *
     * @param string $recipient
     * @param string $status
     * @param string|null $error
     * @param mixed|null $response
     * @return NotificationLog
     */
    protected function createLog(string $recipient, string $status, ?string $error = null, $response = null): NotificationLog
    {
        return NotificationLog::create([
            'channel' => $this->channel,
            'gateway' => $this->gateway,
            'recipient' => $recipient,
            'content' => json_encode($this->data),
            'response' => $response ? json_encode($response) : null,
            'status' => $status,
            'error_message' => $error
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $gateway = $this->getGatewayInstance();
            $response = $gateway->send($this->to, $this->data);

            // Update status to success
            $this->setLogStatus('success', null, $response);
        } catch (\Exception $e) {
            // Update status to failed
            $this->setLogStatus('failed', $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace LaravelMultiNotify\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelMultiNotify\Models\NotificationLog;

abstract class BaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $data;
    protected $gateway;
    protected $channel;

    public function __construct($to, array $data, string $gateway = null)
    {
        $this->to = $to;
        $this->data = $data;
        $this->gateway = $gateway;
    }

    abstract protected function getGatewayInstance();

    protected function logNotification($response, $status = 'success', $error = null)
    {
        return NotificationLog::create([
            'channel' => $this->channel,
            'gateway' => $this->gateway,
            'recipient' => is_array($this->to) ? json_encode($this->to) : $this->to,
            'content' => $this->data,
            'response' => $response,
            'status' => $status,
            'error_message' => $error
        ]);
    }

    public function handle()
    {
        try {
            $gateway = $this->getGatewayInstance();
            $response = $gateway->send($this->to, $this->data);
            $this->logNotification($response);
            return $response;
        } catch (\Exception $e) {
            $this->logNotification(null, 'failed', $e->getMessage());
            throw $e;
        }
    }
}

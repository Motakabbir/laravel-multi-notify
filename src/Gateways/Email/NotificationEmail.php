<?php

namespace LaravelMultiNotify\Gateways\Email;

use Illuminate\Mail\Mailable;

class NotificationEmail extends Mailable
{
    public $data;
    public $templateData;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->templateData = $data['template_data'] ?? [];
    }

    public function build()
    {
        $mail = $this;

        // Handle template or default view
        $view = $this->data['template'] ?? config('multi-notify.email.default_view', 'vendor.multi-notify.emails.default');

        // Set subject
        $mail = $mail->subject($this->data['subject']);

        // Set from address if provided
        $from = $this->getFromAddress();
        if ($from) {
            $mail = $mail->from($from['address'], $from['name'] ?? null);
        }

        // Handle HTML/text email
        if ($this->data['isHtml'] ?? false) {
            $mail = $mail->view($view, array_merge(
                ['content' => $this->data['body']],
                $this->templateData
            ));
        } else {
            $mail = $mail->text($view, array_merge(
                ['content' => $this->data['body']],
                $this->templateData
            ));
        }

        // Handle attachments
        if (!empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachment) {
                if (isset($attachment['content']) && isset($attachment['name'])) {
                    $mail = $mail->attachData(
                        $attachment['content'],
                        $attachment['name'],
                        ['mime' => $attachment['type'] ?? null]
                    );
                }
            }
        }

        return $mail;
    }

    protected function getFromAddress()
    {
        if (isset($this->data['from'])) {
            return is_array($this->data['from'])
                ? $this->data['from']
                : ['address' => $this->data['from'], 'name' => null];
        }

        return config('multi-notify.email.from');
    }
}

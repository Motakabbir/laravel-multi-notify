<?php

namespace LaravelMultiNotify\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationLog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'channel',
        'gateway',
        'recipient',
        'content',
        'response',
        'status',
        'error_message'
    ];
    protected $casts = [
        'content' => 'json',
        'response' => 'json'
    ];
}

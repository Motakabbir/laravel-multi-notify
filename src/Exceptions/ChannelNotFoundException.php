<?php

namespace LaravelMultiNotify\Exceptions;

use Exception;

class ChannelNotFoundException extends Exception
{
    /**
     * Create a new ChannelNotFoundException instance.
     * 
     * @param  string  $message
     * @param  int  $code
     * @param  \Exception|null  $previous
     * @return void
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

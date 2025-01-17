<?php

namespace App\Exception;

use Exception;

class NotFoundException extends Exception
{
    protected $message = "The requested resource was not found.";
    protected $code = 404;

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        if ($message) {
            $this->message = $message;
        }
        if ($code) {
            $this->code = $code;
        }

        parent::__construct($this->message, $this->code, $previous);
    }

    public function getFormattedMessage()
    {
        return [
            "error" => [
                "message" => $this->message,
                "code" => $this->code
            ]
        ];
    }
}

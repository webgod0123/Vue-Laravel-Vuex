<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    private $data;
    
    public function __construct($message, $data = null) {
        parent::__construct($message);
        $this->data = $data;
    }

    public function getError() {
        return [
            'error' => $this->message,
            'fitsysData' => $this->data,
        ];
    }
}

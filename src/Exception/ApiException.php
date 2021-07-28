<?php

namespace App\Exception;

class ApiException extends \Exception
{
    private $publicMessage = '';
    private $httpStatus = 500;

    /*
        throw (new ApiException('error')->withPublicMessage('Un mensaje mas humano')->withHttpStatus(400));
    */

    public function withPublicMessage(string $message): self
    {
        $this->publicMessage = $message;

        return $this;
    }

    public function getPublicMessage(): string
    {
        return $this->publicMessage;
    }

    
    public function withHttpStatus(int $status): self
    {
        $this->httpStatus = $status;

        return $this;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }
}
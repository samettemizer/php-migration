<?php

namespace YD\Exception;

class MigrationException extends \Exception
{
    protected $msg;
    protected $code;
    protected $responseCode;

    /**
     * MigrationException constructor.
     * @param array $msg
     * @param int $code
     * @param int $responseCode
     */
    public function __construct(array $msg, $code = 0,  $responseCode = 200)
    {
        $this->responseCode = $responseCode;
        $this->msg = $msg;
        $this->code = $code;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getCustomCode()
    {
        return $this->code;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
}
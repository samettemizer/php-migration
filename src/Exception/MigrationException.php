<?php

namespace YD\Exception;

/**
 * Class MigrationException
 *
 * @category YD
 * @package  YD\Exception
 * @author   Samet TEMIZER <nestisamet@gmail.com>
 * @license  https://github.com/nestisamet/php-migration/blob/master/LICENSE MIT
 * @link     https://stemizer.com
 */
class MigrationException extends \Exception
{
    protected $msg;
    protected $code;
    protected $responseCode;

    /**
     * MigrationException constructor.
     *
     * @param array $msg          msg
     * @param int   $code         code
     * @param int   $responseCode status code
     */
    public function __construct(array $msg, $code = 0,  $responseCode = 200)
    {
        $this->responseCode = $responseCode;
        $this->msg = $msg;
        $this->code = $code;
    }

    /**
     * Desc
     *
     * @return array
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Desc
     *
     * @return int
     */
    public function getCustomCode()
    {
        return $this->code;
    }

    /**
     * Desc 
     * 
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
}
<?php
namespace autoapi\exception;



class Abort extends \Exception
{
    public function __construct($message, $code = 0) {
        exit(json_encode([$code,$message]));
    }
}
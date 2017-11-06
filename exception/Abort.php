<?php
namespace autoapi\exception;



class Abort extends \Exception
{
    public function __construct($message, $code =-1) {
        exit(json_encode([$code,$message]));
    }
}
<?php
namespace autophp\response;

use Exception;

class Abort extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct(json_encode($code,$message),$code);
    }
}
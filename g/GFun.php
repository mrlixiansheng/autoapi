<?php
namespace autoapi\g;

use autoapi\exception\Abort;

class GFun
{
    public static function abort($message, $code = -1){
        new Abort($message, $code);
    }
}
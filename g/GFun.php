<?php
namespace autoapi\g;

use autoapi\exception\Abort;

class GFun
{
    public static function abort($message, $code = -1){
        new Abort($message, $code);
    }

    static function addonNs(){
      return 'addon';
    }
    static function adminNs(){
        return 'admin';
    }
}
<?php


namespace autoapi\exception;




class Error
{

    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    public static function appException($e){

    }

    public static function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = []){


    }


    public static function appShutdown(){

    }

    protected static function isFatal($type){
    }

    public static function getExceptionHandler(){

    }
}

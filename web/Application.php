<?php

namespace autoapi\web;


use autoapi\domain\RouteDo;

use ReflectionClass;
use Illuminate\Database\Capsule\Manager as Capsule;

class Application
{
    public $defaultRoute = 'site';

    function __construct($config = [])
    {
        $flow=new RouteDo();
        $flow->admin($this);

        $flow->addon($this);
        $flow->reflect($this);
    }




    function db(){
        $capsule = new Capsule;
        $capsule->addConnection(require ROOT.'/com/config/db.php');
        $capsule->bootEloquent();
    }


    function addon(){

    }
     function error(){

     }
}
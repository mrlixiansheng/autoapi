<?php


namespace autoapi\orm;


use ReflectionClass;
use ReflectionProperty;

class ReflectionClassEx
{
    public $refletct;
    public $propertys;
    public function __construct($obj)
    {
        $this->reflect = new ReflectionClass($obj);
        $this->propertys=$this->reflect->getProperties(ReflectionProperty::IS_PUBLIC);
    }
    function getPropertys(){
       return $this->propertys;
    }

}
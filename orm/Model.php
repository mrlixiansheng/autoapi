<?php
namespace autoapi\orm;

abstract class Model extends BaseSql {
    protected $where='id';
    //array("column1" => 1, "column2 > ?" => 2)
    private $select='*';
    private $useWheres=false;
    private $oderBy=null;
    function setWhere($whereStr='id', $useWheres=true){
        $this->where = $whereStr;
        $this->useWheres=$useWheres;
    }

    function setSelect($select){
        $this->select = $select;
    }

    function update($whereParamsArray,$data){
        return  $this->getORM()->where($whereParamsArray)->update($data);
    }
    function save(){
        $data=$this->getMemberVar();
        $is_exist=$this->findOne();
        $where= $this->useWheres?$this->getWheres():$data;
        $id= $is_exist? $this->update($where,$data):$this->insert($data);
        return $id;
    }


    function getWheres(){
        $r= new ReflectionClassEx($this);
        $names=$r->getPropertys();
        $data=[];
        foreach ($names as $name) $this->setDataKV($data,$name);

        return $data;
    }
    function setDataKV(&$data,$name){
        $key=$name->getName();
        $keys=explode(',',$this->where);
        foreach ($keys as $v)  $key==$v&&$data[$key]=$name->getValue($this);
        return false;
    }


    function getMemberVar(){
        $r= new ReflectionClassEx($this);
        $names=$r->getPropertys();
        $data=[];
        foreach ($names as $name){
            $v=$name->getValue($this);
            $n=$name->getName();
            if (null!==$v) $data[$n]=  $v;
        }
        return $data;
    }

    function del(){
        $where=$this->getMemberVar();
        return $this->getORM()->where($where)->delete();
    }
    private function setValues($result){
        $r= new ReflectionClassEx($this);
        $propertys=$r->getPropertys();
        foreach ($propertys as $p){
            $n=$p->getName();
            $v= isset($result[$n])?$result[$n]:null;
            $p->setValue($this,$v);
        }
    }

    function findOne(){
        $where= $this->useWheres?$this->getWheres():$this->getMemberVar();
        $m= $this->getORM()->where($where)->select($this->select)->fetchOne();
        static::setValues($m);
        return $m;
    }
    function findAll(){
        $where= $this->useWheres?$this->getWheres():$this->getMemberVar();
        if ($this->oderBy!=null)return $this->getORM()->where($where)->select($this->select)->order($this->oderBy)->fetchAll();
        return $this->getORM()->where($where)->select($this->select)->fetchAll();
    }


    function count(){
        $where= $this->useWheres?$this->getWheres():$this->getMemberVar();
        $result=$this->getORM()->where($where)->select($this->select)->fetchAll();
        return count($result);
    }
    function all(){
        return $this->getORM()->select('*')->fetchAll();
    }


}
<?php

namespace autoapi\orm;





abstract class BaseSql {
    
    protected static $_db = null;

    
    protected $_table = null;

    
    protected $_rs = [];

    
    protected $_auto = false;


    protected final function __construct() {
        $this->initTable();
    }

    
    public abstract static function getInstance();

    
    protected abstract function initTable();

    
    function auto() {
        if (is_null(self::$_db)) {
            self::$_db = new BasePdo();
        }
        if (!$this->_auto) {
            $this->_auto = true;
            $this->_rs = [];
        }
        return $this;
    }

    
    function exec() {
        $this->_auto = false;
        return $this->_rs;
    }

    protected function querySql($column_name, $where_condition, $order_condition, $limit_condition){
       return "select " . implode(', ', $column_name) . " from {$this->_table} " . $where_condition . $order_condition . $limit_condition;
    }
    protected  function saveSql($keys){
        return "insert into {$this->_table}(" . implode(', ', $keys) . " ) values(:" . implode(', :', $keys) . " )";
    }
    protected  function deleteSql($where_condition){
       return "delete from {$this->_table} " . $where_condition;
    }
    protected  function updateSql(array $model,$where_condition){
        return "update {$this->_table} set " . implode(' = ? , ', array_keys($model)) . " = ? ". $where_condition;
    }



    function buildQuery(array $where_params, array $limit_params = [], array $order_params = [], array $column_name = ['*']) {
        $where_condition = $this->whereCondition($where_params);
        $order_condition = $this->orderCondition($order_params);
        $limit_condition = $this->limitCondition($limit_params);
        $sql = $this->querySql($column_name,$where_condition,$order_condition,$limit_condition);
        $build = ['sql' => $sql, 'params' => array_merge(array_values($where_params), array_values($limit_params))];
        return $this->dbQuery($build);
    }
    function bulidSave(array $model) {
        $keys  = array_keys($model);
        $sql   = $this->saveSql($keys);
        $build = $this->getBuild($sql,$model);
       return   $this->dbExecute($build);
    }

    function bulidDelete(array $where_params) {
        $sql   = $this->deleteSql( $this->whereCondition($where_params));
        $build = $this->getBuild($sql,$where_params);
      return $this->dbExecute($build);
    }

    
    function buildUpdate(array $model, array $where_params) {
        $sql = $this->updateSql($model,$this->whereCondition($where_params));
        $params=array_merge(array_values($model), array_values($where_params));
        $build = $this->getBuild($sql,$params);
        return $this->dbExecute($build);

    }


    private function whereCondition(array $where_params) {
        if (empty($where_params)) return null;
        return "where " . implode(' = ? and ', array_keys($where_params)) . " = ? ";                                                                // return where sql= ? and id= ?
    }
    private function orderConditionArrayWalk($order_params,&$order){
        array_walk($order_params, function($value, $key) use (&$order){
            if(is_string($key) && in_array($value, ['desc', 'asc'])) $order[] = $key . " " . $value;
        });
    }

    private function orderCondition(array &$order_params) {
        if (empty($order_params)) return null;
        $order = [];
       $this->orderConditionArrayWalk($order_params,$order);
        return "order by " . implode(', ', $order) . " ";
    }

    
    private function limitCondition(array $limit_params) {
        if (empty($limit_params) || count($limit_params) != 2) return null;
        return "limit ?, ? ";
    }
    function getBuild($sql, $params){
        return ['sql' => $sql, 'params' => $params];
    }
    protected function dbQuery($build){
        if ($this->_auto) {
            $this->_rs[] = self::$_db->query($build['sql'], $build['params']);
            return $this;
        }
        return $build;
    }
    private function dbExecute($build){
        if ($this->_auto) {
            $this->_rs[] = self::$_db->execute($build['sql'], $build['params']);
            return $this;
        }
        return $build;
    }

    
    
    function __destruct() {
    }
}
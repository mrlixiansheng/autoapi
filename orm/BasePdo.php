<?php


namespace autoapi\orm;



use PDO;
use PDOStatement;

class BasePdo {
    private $pdo = null;
    private $stmt = null;

    public function __construct() {
        $this->pdo = new PDO('mysql:dbname=db_app;host=127.0.0.1:3306', 'root', '123456');
        $this->pdo->query('set names utf8');
    }

    
    public function query($sql, array $params = []) {
        $this->basic($sql, $params);
        return $this->getQueryList();
    }

    
    public function execute($sql, array $params = []) {
        $this->basic($sql, $params);
        return $this->affectedRows();
    }

    
    public function getId($sql, array $params = []) {
        $this->basic($sql, $params);
        return $this->pdo->lastInsertId();
    }

    
    public function packData($sql, callable $func, $params = []) {
        $this->basic($sql, $params);
        return $this->pack($func);
    }

    private function setPreparedStatement($sql) {
        $this->stmt = $this->pdo->prepare($sql);
    }

    
    private function setParams(array $params, PDOStatement &$stmt = null) {
        is_null($stmt) && $stmt = $this->stmt;
        if (!empty($params)) {
            array_walk($params, function($value, $key) use(&$stmt){
                $data_type = PDO::PARAM_STR;
                if (is_int($value)) $data_type = PDO::PARAM_INT;
                elseif (is_bool($value)) $data_type = PDO::PARAM_BOOL;
                $key = is_int($key)? $key + 1: ':' . $key;
                $stmt->bindParam($key, $value, $data_type);
            });
        }
    }

    private function executeStatement() {
        $this->stmt->execute();
    }

    private function affectedRows() {
        if ($this->stmt->errorCode() != PDO::ERR_NONE) return false;
        return $this->stmt->rowCount();
    }

    private function getQueryList($fetch_type = PDO::FETCH_ASSOC) {
        !is_int($fetch_type) && $fetch_type = PDO::FETCH_ASSOC;
        return $this->stmt->fetchAll($fetch_type);
    }

    private function pack(callable $func, PDOStatement &$stmt = null) {
        is_null($stmt) && $stmt = $this->stmt;
        $rs = [];
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null) {
            $func($row);
            $rs[] = $row;
        }
        return $rs;
    }

    private function basic($sql, array $params = []) {
        $this->setPreparedStatement($sql);
        $this->setParams($params);
        $this->executeStatement();
    }

    public function __destruct() {
        if(!is_null($this->stmt) && $this->stmt instanceof PDOStatement) {
            $this->stmt->closeCursor();
            unset($this->stmt);
        }
        unset($this->pdo);
    }
}

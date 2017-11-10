<?php


namespace autoapi\queue;


class Deque{
    public $queue=array();
    //尾入列
    public function addLast($value){
        return array_push($this->queue,$value);
    }
    //尾出列
    public function removeLast(){
        return array_pop($this->queue);
    }
    //头入列
    public function addFirst($value){
        return array_unshift($this->queue,$value);
    }
    //头出列
    public function removeFirst(){
        return array_shift($this->queue);
    }
    //清空队列
    public function makeEmpty(){
        unset($this->queue);
    }
    //获取列头
    public function getFirst(){
        return reset($this->queue);
    }
}
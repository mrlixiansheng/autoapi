<?php
namespace autoapi\util;

class HumpUtil
{
    //下划线转驼峰
    public function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
            return strtoupper($matches[2]);
        },$str);
        return $str;
    }

    // 驼峰转下划线
    public function humpToLine($str){
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '-'.strtolower($matches[0]);
        },$str);
        return $str;
    }

    public function convertHump(array $data){
        $result = [];
        foreach ($data as $key => $item) (is_array($item) || is_object($item))?$result[$this->humpToLine($key)] = $this->convertHump((array)$item):$result[$this->humpToLine($key)] = $item;

        return $result;
    }
}
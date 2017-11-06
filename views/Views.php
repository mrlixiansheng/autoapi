<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/5
 * Time: 12:52
 */

namespace autoapi\views;


class Views
{
    // 视图实例
    protected static $instance;
    // 模板引擎实例
    public $engine;
    // 模板变量
    protected $data = [];
    // 用于静态赋值的模板变量
    protected static $var = [];
    // 视图输出替换
    protected $replace = [];







    /**
     * 解析和获取模板内容 用于输出
     * @param string    $template 模板文件名或者内容
     * @param array     $vars     模板输出变量
     * @param array     $replace 替换内容
     * @param array     $config     模板参数
     * @param bool      $renderContent     是否渲染内容
     * @return string
     * @throws Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        // 模板变量
        $vars = array_merge(self::$var, $this->data, $vars);

        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        // 渲染输出
        $method = $renderContent ? 'display' : 'fetch';
        $this->engine->$method($template, $vars, $config);

        // 获取并清空缓存
        $content = ob_get_clean();
        // 内容过滤标签
       // Hook::listen('view_filter', $content);
        // 允许用户自定义模板的字符串替换
        $replace = array_merge($this->replace, $replace);
        if (!empty($replace)) $content = strtr($content, $replace);

        return $content;
    }

}
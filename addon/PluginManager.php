<?php


class PluginManager
{
    //监听已注册插件
    private $_listeners = array();

    public function __construct()
    {

        //函数实现后的最终数据结构效果如下
        $plugins=array(array("directory"=>"demo",
            "name"=>"DEMO"));


        if($plugins)
        {
            foreach($plugins as $plugin)

            {//假定每个插件文件夹中包含一个actions.php文件，它是插件的具体实现
                if (@file_exists(STPATH .'plugins/'.$plugin['directory'].'/actions.php'))
                {
                    include_once(STPATH .'plugins/'.$plugin['directory'].'/actions.php');
                    $class = $plugin['name'].'_actions';
                    if (class_exists($class))
                    {
                        //初始化所有插件
                        //$this 是本类的引用
                        new $class($this);
                    }
                }
            }
        }
        #此处做些日志记录方面的东西
    }

  //注册需要监听的插件方法（钩子）
    function register($hook, &$reference, $method)
    {

        $key = get_class($reference).'->'.$method;

        $this->_listeners[$hook][$key] = array(&$reference, $method);                                                                                                                            //将插件的引用连同方法push进监听数组中

    }
    //触发一个钩子
    function trigger($hookName, $data='')
    {
        $result = '';

        if (isset($this->_listeners[$hookName]) && is_array($this->_listeners[$hookName]) && count($this->_listeners[$hookName]) > 0)
        {

            foreach ($this->_listeners[$hookName] as $listener)
            {

                $class =& $listener[0];
                $method = $listener[1];
                if(method_exists($class,$method)) $result .= $class->$method($data);   // 取出插件对象的引用和方法

            }
        }

        return $result;
    }
}

define('STPATH', "./");

$pluginManager=new PluginManager();

$pluginManager->trigger("demo");
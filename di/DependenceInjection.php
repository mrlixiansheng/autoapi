<?php
namespace PhalApi;

use ArrayAccess;
use Closure;

/**
 * DependenceInjection 依赖注入类
 *
 *  Dependency Injection 依赖注入容器
 *  
 * - 调用的方式有：set/get函数、魔法方法setX/getX、类变量$fdi->X、数组$fdi['X]
 * - 初始化的途径：直接赋值、类名、匿名函数
 *
 * <br>使用示例：<br>
```
 *       $di = new DependenceInjection();
 *      
 *       // 用的方式有：set/get函数  魔法方法setX/getX、类属性$di->X、数组$di['X']
 *       $di->key = 'value';
 *       $di['key'] = 'value';
 *       $di->set('key', 'value');
 *       $di->setKey('value');
 *      
 *       echo $di->key;
 *       echo $di['key'];
 *       echo $di->get('key');
 *       echo $di->getKey();
 *      
 *       // 初始化的途径：直接赋值、类名(会回调onInitialize函数)、匿名函数
 *       $di->simpleKey = array('value');
 *       $di->classKey = 'DependenceInjection';
 *       $di->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
```       
 *      
 * @property \PhalApi\Request        $request    请求
 * @property \PhalApi\Response_Json  $response   结果响应
 * @property \PhalApi\Cache          $cache      缓存
 * @property \PhalApi\Crypt          $crypt      加密
 * @property \PhalApi\Config         $config     配置
 * @property \PhalApi\Logger         $logger     日记
 * @property \PhalApi\DB\NotORM      $notorm     数据库
 * @property \PhalApi\Loader         $loader     自动加载
 * @property \PhalApi\Helper\Tracer  $tracer     全球追踪器

 */ 

class DependenceInjection implements ArrayAccess {


    protected static $instance = NULL;

    //服务命中的次数
    protected $hitTimes = array();
    
    //注册的服务池

    protected $data = array();

    public function __construct() {

    }

    //获取DI单体实例
    public static function one() {
        if (static::$instance == NULL) {
            static::$instance = new DependenceInjection();
            static::$instance->onConstruct();
        }

        return static::$instance;
    }

    //service级的构造函数
    public function onConstruct() {
        $this->request = '\\PhalApi\\Request';
        $this->response = '\\PhalApi\\Response\\JsonResponse';
        $this->tracer = '\\PhalApi\\Helper\\Tracer';
    }

    public function onInitialize() {
    }

    //统一setter

    public function set($key, $value) {
        $this->hitTimes[$key] = 0;
        $this->data[$key] = $value;
        return $this;
    }

    // 统一getter

    public function get($key, $default = NULL) {
        if (!isset($this->data[$key])) $this->data[$key] = $default;
        if (!isset($this->hitTimes[$key])) $this->hitTimes[$key] = 0;
        $this->hitTimes[$key] ++;
        if ($this->hitTimes[$key] == 1) $this->data[$key] = $this->initService($this->data[$key]);
        return $this->data[$key];
    }

    //魔法方法

    public function __call($name, $arguments) {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : NULL);
        } else if (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : NULL);
        }

        throw new InternalServerErrorException(T('Call to undefined method DependenceInjection::{name}() .', array('name' => $name)));
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __get($name) {
        return $this->get($name, NULL);
    }

    //ArrayAccess（数组式访问）接口

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetGet($offset) {
        return $this->get($offset, NULL);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    protected function initService($config) {
        $rs = NULL;
        if ($config instanceOf Closure) $rs = $config();
         elseif (is_string($config) && class_exists($config)) {
            $rs = new $config();
            if(is_callable(array($rs, 'onInitialize'))) call_user_func(array($rs, 'onInitialize'));
        } else $rs = $config;
        return $rs;
    }
}


<?php


namespace autoapi\route;


class Router{


    /**
     *  Set the directory name
     *
     * @access  public
     * @param   string
     * @return  void
     */
    function set_directory($dir) {
        $this->directory = $dir . '/';
    }



    function _validate_request($segments) {
        if (count($segments) == 0) return $segments;
        //根目录
      $this->parseRoot($segments);
      $this->parseSubRoot($segments);


       // show_404($segments[0]);
    }

    function parseSubRoot($segments){
        if (is_dir(APPPATH . 'controllers/' . $segments[0])) {
            $temp = array('dir' => array(), 'path' => APPPATH . 'controllers/');
            foreach ($segments as $k => $v) {
                $temp['path'] .= $v;
                if (!is_dir($temp['path'])) continue;
                $temp['dir'][] = $v;
                unset($segments[$k]);
            }
            $this->set_directory(implode('/', $temp['dir']));
            $segments = array_values($segments);
            unset($temp);

            if (count($segments) > 0) {
                if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . '.php')) {
                    if (!empty($this->routes['404_override'])) {
                        $x = explode('/', $this->routes['404_override']);
                        $this->set_directory('');
                        $this->set_class($x[0]);
                        $this->set_method(isset($x[1]) ? $x[1] : 'index');
                        return $x;
                    } else show_404($this->fetch_directory() . $segments[0]);

                }
            } else $this->parseDefaultController();

            return $segments;
        }
    }
    function   parseRoot(&$segments){
        if (file_exists(APPPATH . 'controllers/' . $segments[0] . '.php')) return $segments;
    }


    function parseIndex(){

        if (!empty($this->routes['404_override'])) {
            $x = explode('/', $this->routes['404_override']);
            $this->set_class($x[0]);
            $this->set_method(isset($x[1]) ? $x[1] : 'index');
            return $x;
        }
    }
    function parseDefaultController(){
        if (strpos($this->default_controller, '/') !== FALSE) {
            $x = explode('/', $this->default_controller);
            $this->set_class($x[0]);
            $this->set_method($x[1]);
        } else {
            $this->set_class($this->default_controller);
            $this->set_method('index');
        }

        if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $this->default_controller . '.php')) {
            $this->directory = '';
            return array();
        }
    }


}
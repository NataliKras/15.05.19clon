<?php

class Router
{
    public $controllerName;
    public $actionName;
    public $url;

    public function __construct()
    {
        $decodeUri = URL::getInstance()->decodeUri($_SERVER['REQUEST_URI']);

//        URL::getInstance()->to('TableOne/ShowTable');

        if ($decodeUri !== null) {
            $handler = explode('/', $decodeUri['handler']);
            $this->controllerName = $handler[0] . 'Controller';
            $this->actionName = 'action' . $handler[1];
            $_GET = array_merge($_GET, $decodeUri['vars']);
        } elseif (isset($_GET["a"])) {
            $this->controllerName = ($_GET["t"] ?? Conf::DEFAULT_CONTROLLER) . 'Controller';
            $this->actionName = 'action' . $_GET["a"];
        }
    }


    public function run()
    {
        if (Auth::checkControllerPermit($this->controllerName)) {
            if (class_exists($this->controllerName)) {

                $MVC = new $this->controllerName();

                if (method_exists($MVC, $this->actionName)) {
                    $MVC->{$this->actionName}();
                } else {
                    // echo "нет такого метода: $this->methodName";
                    (new ErrorController())->notFoundAction($this->actionName);
                }
            } else {
                // echo "нет такого класса: $this->controllerName";
                (new ErrorController())->notFoundController($this->controllerName);
            }
        } else {
            // echo "ошибка доступа";
            (new ErrorController())->forbiddenController();
        }
    }
}

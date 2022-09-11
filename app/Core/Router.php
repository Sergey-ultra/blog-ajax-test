<?php

namespace App\Core;
use App\Model\User;



class Router
{
    protected $routes = [];
    protected $params = [];


    public function __construct()
    {
        $arr = require_once   __DIR__ .'/../config/routes.php';
        foreach ($arr as $route) {
            $this->add($route);
        }
    }

    public function add(array $route): void
    {
        $uri = $route['uri'];
        $route['uri'] ="#^$uri$#";
        $this->routes[] = $route;
    }

    public function match(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = str_replace('/public/','', $_SERVER['REQUEST_URI']);

        $uri = explode('?', $uri, 2)[0];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['uri'], $uri, $matches)) {
                $this->params = $route;
                return true;
            }
        }
        return false;
    }

    private function getCurrentUser(): ?User
    {
        $headers = getallheaders();
        $bearerToken = $headers['Authorization'];
        $token = mb_substr($bearerToken, 7, null, 'UTF-8');
        [$id, $token] = explode('|', $token, 2);


        if ($curUser = (new User())->findUserById((int) $id)) {
            return hash_equals($curUser->token, hash('sha256', $token)) ? $curUser : null;
        }
        return null;
    }


    public function isAdmin():bool
    {
        $user = $this->getCurrentUser();
        if (!isset($user)) {
            return false;
        }
        return $user->isAdmin();
    }

    public function run()
    {
        if (!$this->match()) {
            http_response_code(404);
            echo "Маршрут не найден";
        } else {
            $this->checkMiddleware();
            $this->call();
        }
    }


    protected function call()
    {
        $class =  $this->params['controller'];

        if (!class_exists($class)) {
            echo "Класса нет";
        } else {
            $action = $this->params['action'];
            if (!method_exists($class, $action)) {
                echo "Метода класса нет";
            } else {
                $controller = new $class;
                $controller->$action();
            }
        }
    }

    protected function checkMiddleware()
    {
        if (key_exists('middleware', $this->params)) {
            $middleware = $this->params['middleware'];

            if ($middleware === 'auth' && !$this->getCurrentUser()) {
                header('X-PHP-Response-Code: 401', true, 401);
                exit;
            }

            if ($middleware === 'isAdmin' &&   !$this->isAdmin()){
                header('X-PHP-Response-Code: 401', true, 401);
                exit ;
            }
        }
    }
}
<?php 
class Router {
    private $routes = [];
    
    public function add($route, $params = []) {
        $this->routes[$route] = $params;
    }
    
    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller . 'Controller';
            
            if (class_exists($controller)) {
                $controller_object = new $controller();
                
                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);
                
                if (is_callable([$controller_object, $action])) {
                    $params = $this->params['params'] ?? [];
                    call_user_func_array([$controller_object, $action], $params);
                } else {
                    throw new Exception("Método $action no encontrado en controlador $controller");
                }
            } else {
                throw new Exception("Controlador $controller no encontrado");
            }
        } else {
            throw new Exception('No se encontró la ruta', 404);
        }
    }
    
    private function match($url) {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($params as $key => $val) {
                    $this->params[$key] = $val;
                }
                
                // Obtener parámetros de la URL
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $this->params['params'][$key] = $match;
                    }
                }
                
                return true;
            }
        }
        return false;
    }
    
    private function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    
    private function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }
    
    private function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        
        return rtrim($url, '/');
    }
    
    private function getNamespace() {
        return '';
    }
}


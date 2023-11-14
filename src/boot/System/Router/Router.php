<?php

/*
* @name        Route base class
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Основной класс для роутинга
*/

namespace Boot\System\Router;

use \Boot\Services\Request;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\BindingRegistrar;
use Illuminate\Contracts\Routing\Registrar as RegistrarContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Boot\System\Http\JsonResponse;
use Illuminate\Http\Request as lr;
use Boot\System\Http\Response;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use ReflectionClass;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Router
{
    public $alias = [];
    public $routes = [];

    // protected $baseRoute = '';
    protected $lastMount = null;
    public $currentRouterPattern = null;

    // public $alias = [];

    /**
     * @var array The route patterns and their handling functions
     */
    public $afterRoutes = array();

    /**
     * @var array The before middleware route patterns and their handling functions
     */
    public $beforeRoutes = array();

    /**
     * @var object|callable The function to be executed when no route has been matched
     */
    protected $notFoundCallback;

    /**
     * @var string Current base route, used for (sub)route mounting
     */
    protected $baseRoute = '';

    /**
     * @var string The Request Method that needs to be handled
     */
    protected $requestedMethod = '';

    /**
     * @var string The Server Base Path for Router Execution
     */
    protected $serverBasePath;

    /**
     * @var string Default Controllers Namespace
     */
    protected $namespace = '';

    /**
     * @var array Default Controllers Namespace
     */
    protected $action = [];

    /**
     * Массив последних значений (methods, pattern, fn)
     *
     * @var array
     */
    protected $last = [];

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью GET.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function get($pattern, $fn)
    {
        return  $this->matching('GET', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью POST.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function post($pattern, $fn)
    {
        return $this->matching('POST', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью DELETE.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function delete($pattern, $fn)
    {
        return $this->matching('DELETE', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью OPTIONS.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function options($pattern, $fn)
    {
        return $this->matching('OPTIONS', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью PUT.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function put($pattern, $fn)
    {
        return $this->matching('PUT', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется с помощью PATCH.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function patch($pattern, $fn)
    {
        return $this->matching('PATCH', $pattern, $fn);
    }

    /**
     * Сокращенное обозначение маршрута, доступ к которому осуществляется через все методы.
     *
     * @param string          $pattern Шаблон маршрута, такой как /about/system
     * @param object|callable $fn      Выполняемая функция
     */
    public function all($pattern, $fn)
    {
        return $this->matching('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }

    public function getName($name, $params = null)
    {
        if (isset($this->alias[$name])) {
            $alias = $this->alias[$name];
            $count = preg_match_all('/\/{(.*?)}/', $alias, $matches, PREG_SET_ORDER);
            $paramsArray = [];
            if (!empty($params)) {
                if (!is_array($params)) {
                    $paramsArray[] = $params;
                } else {
                    $paramsArray = $params;
                }
            }
            $currentParams = $paramsArray;
            if ($count == 0) {
                $currentName = $this->alias[$name];
            } else {
                $currentName = preg_replace_callback('/\/{(.*?)}/', function ($v) use (&$currentParams, $paramsArray, $count) {
                    if (isset($paramsArray[$v[1]])) {
                        $result = '/' . $paramsArray[$v[1]];
                        unset($currentParams[$v[1]]);
                        return $result;
                    } elseif ($count <= count($paramsArray)) {
                        if (isset($paramsArray[0])) {
                            $result = '/' . $paramsArray[0];
                            array_shift($currentParams);
                            return $result;
                        } else {
                            throw new \Exception('Не найде аргумент: ' . $v[1]);
                        }
                    } else {
                        throw new \Exception('Не найде аргумент: ' . $v[1]);
                    }
                }, $alias);
            }
            if (!empty($currentParams)) {
                $getParams = [];
                foreach ($currentParams as $key => $value) {
                    if (is_numeric($key)) {
                        $getParams[] = $value;
                    } else {
                        $getParams[] = $key . '=' . $value;
                    }
                }
                $currentName .= '?' . implode('&', $getParams);
            }
            return  $currentName;
        } else {
            throw new \Exception('Имя маршрутизации ' . $name . ' не найдено!');
        }
    }



    public function matching($methods, $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;
        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
            );
        }
        $m = $methods;
        if (isset($this->router[$pattern]['method'])) {
            $mArrayFirst = explode('|', $this->router[$pattern]['method']);
            $mArrayLast = explode('|', $methods);
            $m = implode('|', array_merge($mArrayFirst, $mArrayLast));
        }
        $this->router[$pattern] = [
            'method' => $m,
            'uri' => $pattern,
            'action' => $fn,
        ];

        $this->last = [
            'methods' => $methods,
            'pattern' => $pattern,
            'fn' => $fn,
        ];
        return $this;
    }

    public function name($name)
    {
        if (!empty($this->last['pattern'])) {
            $this->setAlias($this->last['pattern'], $this->mountName . $name);
        }
    }

    protected function setAlias($pattern, $name)
    {
        $this->router[$pattern]['name'] = $name;
        $this->alias[$name] = $pattern;
    }

    public $mountName = '';

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string   $baseRoute The route sub pattern to mount the callbacks on
     * @param callable $fn        The callback method
     */
    public function mount($baseRoute, $fn, $name = null)
    {
        // Track current base route
        $curBaseRoute = $this->baseRoute;
        $curMountName = $this->mountName;

        // Build new base route string
        $this->baseRoute .= $baseRoute;

        $this->mountName .= $name;

        // dump($name);
        // Call the callable
        call_user_func($fn);

        // Restore original base route
        $this->baseRoute = $curBaseRoute;
        $this->mountName = $curMountName;
    }


    public $resourcePattern = [
        'index' => '',
        'create' => '/create',
        'store' => '',
        'grid' => '/grid',
        'edit' => '/{id}/edit',
        'show' => '/{id}',
        'update' => '/{id}',
        'destroy' => '/{id}',
    ];

    /**
     * Создание стандартных маршрутов роутинга
     *
     * @param string    $pattern Шаблон маршрута, например \news
     * @param object    $fn Контроллер для обработки стандартных маршрутов
     */
    public function resource($pattern, $fn)
    {
        $this->matching('GET', $pattern, [$fn, 'index']);
        $this->matching('GET', $pattern . '/create', [$fn, 'create']);
        $this->matching('POST', $pattern, [$fn, 'store']);
        $this->matching('POST|GET', $pattern . '/grid', [$fn, 'grid']);
        $this->matching('GET', $pattern . '/{id}/edit', [$fn, 'edit']);
        $this->matching('GET', $pattern . '/{id}', [$fn, 'show']);
        $this->matching('PUT|PATCH', $pattern . '/{id}', [$fn, 'update']);
        $this->matching('DELETE', $pattern . '/{id}', [$fn, 'destroy']);
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;
        $this->last['pattern'] = $pattern;
        return $this;
    }

    public function names($names)
    {
        if (is_array($names)) {
            foreach ($names as $key => $value) {
                if (isset($this->resourcePattern[$key])) {
                    $this->setAlias($this->last['pattern'] . $this->resourcePattern[$key], $this->mountName . $value);
                } else {
                    throw new \Exception('Указан не верный параметр ресурса');
                }
            }
        } else {
            foreach ($this->resourcePattern as $key => $value) {
                $this->setAlias($this->last['pattern'] . $value, $this->mountName . $names . '.' . $key);
            }
        }
    }


    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes       Collection of route patterns and their handling functions
     * @param bool  $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     */
    protected function handle($routes, $quitAfterRun = false)
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $uri = $this->getCurrentUri();
        // dd($uri);
        // Loop all routes
        foreach ($routes as $route) {
            // Replace all curly braces matches {} into word patterns (like Laravel)
            $crp = $route['pattern'];
            $route['pattern'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['pattern']);

            // dd($route);
            // we have a match!
            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);

                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function ($match, $index) use ($matches) {

                    // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        if ($matches[$index + 1][0][1] > -1) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                        }
                    } // We have no following parameters: return the whole lot

                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                // dd($params);
                // Call the handling function with the URL parameters if the desired input is callable
                $this->currentRouterPattern = $crp;
                $this->invoke($route['fn'], $params);

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }


    public function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));

        // Don't take query params into account on the URL
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }


    public function getBasePath()
    {
        // Check if server base path is defined, if not define it.
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return $this->serverBasePath;
    }

    public function setBasePath($serverBasePath)
    {
        $this->serverBasePath = $serverBasePath;
    }

    public function trigger404()
    {
        if ($this->notFoundCallback) {
            $this->invoke($this->notFoundCallback);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
    }

    public function set404($fn)
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string          $methods Allowed methods, | delimited
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function before($methods, $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
            );
        }
    }

    public function run($callback = null)
    {
        // Define which method we need to handle
        $this->requestedMethod = $this->getRequestMethod();

        // Handle all before middlewares
        if (isset($this->beforeRoutes[$this->requestedMethod])) {
            $this->handle($this->beforeRoutes[$this->requestedMethod]);
        }

        // Handle all routes
        $numHandled = 0;
        if (isset($this->afterRoutes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$this->requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            $this->trigger404();
        } // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }


    public function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }


    public function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        }

        // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
            if (isset($_POST['_method'])) {
                switch ($_POST['_method']) {
                    case 'PUT':
                        $method = 'PUT';
                        break;
                    case 'DELETE':
                        $method = 'DELETE';
                        break;
                    case 'PATCH':
                        $method = 'PATCH';
                        break;
                }
            }
        }
        return $method;
    }

    public function getRequestHeaders()
    {
        $headers = array();

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    private function paramsPrepare($arg, $params)
    {
        foreach ($arg as $key => $value) {
            $reflectionFlag = false;
            if ($value instanceof \ReflectionParameter) {
                $reflectionParameterType = $value->getType();
               
                if ($reflectionParameterType instanceof \ReflectionNamedType) {
                    $reflectionParameterClass = $reflectionParameterType->getName();
                
                    if (class_exists($reflectionParameterClass)) {
                        $newReflection = new ReflectionClass($reflectionParameterClass);
                        if($reflectionParameterClass == Request::class || $newReflection->isSubclassOf(\Boot\Abstracts\Requests\Request::class)){
                            $reflectionFlag = true;
                        }
                    }
                }
            }
           
            if (!isset($params[$key]) || $reflectionFlag) {
                if ($reflectionFlag) {
                    array_splice($params, $key, 0, [new $reflectionParameterClass]);
                } else {
                    if ($value->isOptional()) {
                        $params[$key] = $value->getDefaultValue();
                    } else {
                        throw new \Exception('Не верное количество аргументов');
                    }
                }
            }
        }
        return $params;
    }

    private function invoke($fn, $params = array())
    {

        if (is_callable($fn)) {

            if (is_array($fn)) {
                $arg = (new \ReflectionMethod($fn[0], $fn[1]))->getParameters();
            } else {
                $arg = (new \ReflectionFunction($fn))->getParameters();
            }
            $params = $this->paramsPrepare($arg, $params);
            call_user_func_array($fn, $params);
        }
        // If not, check the existence of special parameters
        elseif (is_string($fn) && (stripos($fn, '@') !== false)) {

            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);
            // Adjust controller class if namespace has been set
            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }
            // Make sure it's callable
            if (is_callable(array($controller, $method))) {
                if ((new \ReflectionMethod($controller, $method))->isStatic()) {

                    forward_static_call_array(array($controller, $method), $params);
                } else {
                    // Make sure we have an instance, to prevent "non-static method … should not be called statically" warnings
                    if (\is_string($controller)) {
                        $controller = new $controller();
                    }
                    call_user_func_array(array($controller, $method), $params);
                }
            }
        } elseif (is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])) {
            list($controller, $method) = $fn;
            // Adjust controller class if namespace has been set

            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }

            if (\is_string($controller)) {
                $controller = new $controller();
            }
            $arg = (new \ReflectionMethod($fn[0], $fn[1]))->getParameters();
            // dd($arg);
            $params = $this->paramsPrepare($arg, $params);
            $response = call_user_func_array(array($controller, $method), $params);
            // dd($response);



            // if ($response instanceof Responsable) {
            //     $response = $response->toResponse($request);
            // }

            if ($response instanceof Model && $response->wasRecentlyCreated) {
                $response = new JsonResponse($response, 201);
            } elseif ($response instanceof Stringable) {
                $response = new Response($response->__toString(), 200, ['Content-Type' => 'text/html']);
            } elseif (
                !$response instanceof SymfonyResponse &&
                ($response instanceof Arrayable ||
                    $response instanceof Jsonable ||
                    $response instanceof JsonSerializable ||
                    $response instanceof \stdClass ||
                    is_array($response))
            ) {
                $response = new JsonResponse($response);
            } elseif (!$response instanceof SymfonyResponse) {
                $response = new Response($response, 200, ['Content-Type' => 'text/html']);
            }

            if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
                $response->setNotModified();
            }
            $response->send();
        } else {
            throw new \Exception('Не найден вызываемый метод ' . implode('@', $fn));
        }
    }

    /**
     * middleware
     *
     * @param  mixed $middleware
     * @return void
     */
    public function middleware($middleware = null)
    {
        if (is_string($middleware)) {
            $middleware = func_get_args();
            if (!empty($this->last)) {
                $this->before($this->last['methods'], $this->last['pattern'], $middleware);
            }
        }
        return $this;
    }
}

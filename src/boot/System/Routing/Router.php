<?php

namespace Boot\System\Routing;

use stdClass;
use ArrayObject;
use JsonSerializable;
use Illuminate\Routing\Route;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Model;
use Boot\System\Routing\ResourceRegistrar;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Events\RouteMatched;
use Boot\System\Http\Request as RequestCore;
use Illuminate\Contracts\Support\Responsable;
use Boot\System\Http\Response as ResponseCore;
use Boot\System\Routing\PendingResourceRegistration;
use Boot\System\Http\JsonResponse as JsonResponseCore;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class Router extends \Illuminate\Routing\Router
{
    protected function runRouteWithinStackCore(Route $route, RequestCore $request)
    {
        $shouldSkipMiddleware = $this->container->bound('middleware.disable') &&
            $this->container->make('middleware.disable') === true;

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        return (new Pipeline($this->container))
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($route) {
                return $this->prepareResponse(
                    $request,
                    $route->run()
                );
            });
    }

    public function respondWithRoute($name)
    {
        $route = tap($this->routes->getByName($name))->bind($this->currentRequest);

        return $this->runRouteCore($this->currentRequest, $route);
    }

    protected function runRouteCore(RequestCore $request, Route $route)
    {
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $this->events->dispatch(new RouteMatched($route, $request));

        return $this->prepareResponse($request,
            $this->runRouteWithinStackCore($route, $request)
        );
    }


    public function dispatchCore(RequestCore $request)
    {
        $this->currentRequest = $request;

        return $this->dispatchToRouteCore($request);
    }

    public function dispatchToRouteCore(RequestCore $request)
    {
        return $this->runRouteCore($request, $this->findRoute($request));
    }

    public static function toResponse($request, $response)
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
            $response = new JsonResponseCore($response, 201);
        } elseif ($response instanceof Stringable) {
            $response = new ResponseCore($response->__toString(), 200, ['Content-Type' => 'text/html']);
        } elseif (! $response instanceof SymfonyResponse &&
                   ($response instanceof Arrayable ||
                    $response instanceof Jsonable ||
                    $response instanceof ArrayObject ||
                    $response instanceof JsonSerializable ||
                    $response instanceof stdClass ||
                    is_array($response))) {
            $response = new JsonResponseCore($response);
        } elseif (! $response instanceof SymfonyResponse) {
            $response = new ResponseCore($response, 200, ['Content-Type' => 'text/html']);
        }

        if ($response->getStatusCode() === ResponseCore::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }

     /**
     * Register an array of resource controllers.
     *
     * @param  array  $resources
     * @param  array  $options
     * @return void
     */
    public function resources(array $resources, array $options = [])
    {
        foreach ($resources as $name => $controller) {
            $this->resource($name, $controller, $options);
        }
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(ResourceRegistrar::class)) {
            $registrar = $this->container->make(ResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        return new PendingResourceRegistration(
            $registrar, $name, $controller, $options
        );
    }

    
}

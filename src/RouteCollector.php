<?php

namespace Mfw;

/**
 * Class RouteCollector (with middleware support)
 * @package Mfw
 */
class RouteCollector extends \FastRoute\RouteCollector
{
    protected $middlewares = [];

    /**
     * @param mixed $httpMethod
     * @param mixed $route
     * @param mixed $handler
     */
    public function addRoute($httpMethod, $route, $handler)
    {
        $handler = array_merge($this->middlewares, (array)$handler);
        parent::addRoute($httpMethod, $route, $handler);
    }

    /**
     * @param mixed $prefix
     * @param callable $callback
     * @param array $middlewares
     */
    public function addGroup($prefix, callable $callback, array $middlewares = [])
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousMiddlewares = $this->middlewares;

        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        $this->middlewares = array_merge($previousMiddlewares, $middlewares);

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->middlewares = $previousMiddlewares;
    }
}
<?php

namespace Mfw;

use FastRoute\Dispatcher;
use Mfw\Exception\AppException;
use Mfw\Exception\HttpException;
use Mfw\Interfaces\ResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

/**
 * Class WebApp
 */
class WebApp implements RequestHandlerInterface
{
    /** @var ResolverInterface */
    private $resolver;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var array */
    private $middleware = [];

    /**
     * @param Dispatcher $dispatcher
     * @param ResolverInterface $resolver
     * @param array $middleware
     */
    public function __construct(Dispatcher $dispatcher, ResolverInterface $resolver, array $middleware = [])
    {
        $this->resolver = $resolver;
        $this->dispatcher = $dispatcher;
        $this->middleware = $middleware;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws HttpException
     * @throws AppException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $chain = array_merge($this->middleware, (array)$routeInfo[1]);

                $relay = new Relay($chain, function (string $entry) {
                    return $this->resolver->resolve($entry);
                });

                $request = $this->buildAttributes($request, $routeInfo[2]);

                return $relay->handle($request);
            case Dispatcher::NOT_FOUND:
                throw new HttpException('Not found', 404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('Not allowed', 405);
            default:
                throw new AppException("Unknown action with code '{$routeInfo[0]}'");
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $attributes
     * @return ServerRequestInterface
     */
    protected function buildAttributes(ServerRequestInterface $request, array $attributes): ServerRequestInterface
    {
        foreach ($attributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        return $request;
    }
}
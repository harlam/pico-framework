<?php

namespace Mfw;

use Mfw\Exception\AppException;
use Mfw\Interfaces\ResolverInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Class Resolver
 * @package Mfw
 */
class Resolver implements ResolverInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @throws ReflectionException
     * @throws AppException
     */
    public function __invoke($value)
    {
        return $this->resolve($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     * @throws ReflectionException
     * @throws AppException
     */
    public function resolve($value)
    {
        if (is_callable($value)) {
            return $value;
        }

        if ($this->container->has($value)) {
            return $this->container->get($value);
        }

        $reflection = new ReflectionClass($value);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $constructorParams = [];
        foreach ($constructor->getParameters() as $param) {
            $class = $param->getClass();

            if ($class === null) {
                throw new AppException('Argument type is required');
            }

            $constructorParams[] = $this->container->get($class->getName());
        }

        return $reflection->newInstanceArgs($constructorParams);
    }
}
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
     * @param string $uid
     * @return mixed
     * @throws ReflectionException
     * @throws AppException
     */
    public function __invoke(string $uid)
    {
        return $this->resolve($uid);
    }

    /**
     * @param string $uid
     * @return mixed
     * @throws ReflectionException
     * @throws AppException
     */
    public function resolve(string $uid)
    {
        if ($this->container->has($uid)) {
            return $this->container->get($uid);
        }

        $reflection = new ReflectionClass($uid);

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
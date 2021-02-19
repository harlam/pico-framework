<?php

namespace Mfw\Interfaces;

interface ResolverInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolve($value);
}
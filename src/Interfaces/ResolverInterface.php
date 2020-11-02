<?php

namespace Mfw\Interfaces;

interface ResolverInterface
{
    /**
     * @param string $value
     * @return mixed
     */
    public function resolve(string $value);
}
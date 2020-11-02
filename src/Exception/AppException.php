<?php

namespace Mfw\Exception;

use Exception;

class AppException extends Exception
{
    /** @var array */
    protected $context = [];

    /**
     * @param array $context
     * @return AppException
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
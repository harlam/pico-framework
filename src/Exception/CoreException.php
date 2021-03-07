<?php

namespace Mfw\Exception;

use Exception;

class CoreException extends Exception
{
    /** @var array */
    protected $context = [];

    /**
     * @param array $context
     * @return CoreException
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

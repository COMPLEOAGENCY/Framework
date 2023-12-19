<?php

namespace Framework;

class MiddlewareRegistry
{
    private $path;
    private $middlewareClass;

    public function __construct()
    {

    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getMiddlewareClass(): string
    {
        return $this->middlewareClass;
    }

    public function setMiddlewareClass(string $middlewareClass): self
    {
        $this->middlewareClass = $middlewareClass;
        return $this;
    }
}

<?php

namespace Oaktree;

class Css
{
    public string $state;

    public function __construct(protected array|string $declarations) {}

    public function state(string $state)
    {
        $this->state = $state;

        return $this;
    }

    public function toString(): string
    {
        return is_array($this->declarations) ? implode("\n", $this->declarations) : $this->declarations;
    }
}

<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RecupEnv
{
    public function __construct(
        $demo
    ) {
    }

    public function getDemo(): string
    {
        return $this->demo;
    }
}

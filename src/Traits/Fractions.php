<?php

namespace Oaktree\Traits;

trait Fractions
{
    /**
     * Fractional width mappings
     */
    protected array $fractions = [
        '1/2' => '50%',
        '1/3' => '33.333333%',
        '2/3' => '66.666667%',
        '1/4' => '25%',
        '2/4' => '50%',
        '3/4' => '75%',
        '1/5' => '20%',
        '2/5' => '40%',
        '3/5' => '60%',
        '4/5' => '80%',
        '1/12' => '8.333333%',
        '6/12' => '50%',
        '12/12' => '100%',
    ];

    protected function hasFraction(string $fraction)
    {
        return isset($this->fractions[$fraction]);
    }

    protected function fractionToPercent(string $fraction)
    {
        return $this->fractions[$fraction] ?? null;
    }
}

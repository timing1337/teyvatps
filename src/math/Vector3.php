<?php

namespace TeyvatPS\math;

class Vector3
{
    public function __construct(private float $x = 0, private float $y = 0, private float $z = 0)
    {
    }

    public function compare(Vector3 $vector): bool
    {
        return ($this->x === $vector->x && $this->y === $vector->y && $this->z === $vector->z);
    }

    public function toProto(): \Vector
    {
        return (new \Vector())->setX($this->x)->setY($this->y)->setZ($this->z);
    }

    public function __toString(): string
    {
        return sprintf('Vector3(%f, %f, %f)', $this->x, $this->y, $this->z);
    }

    public function setZ(float $z): void
    {
        $this->z = $z;
    }

    public function setY(float $y): void
    {
        $this->y = $y;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getZ(): float
    {
        return $this->z;
    }
}
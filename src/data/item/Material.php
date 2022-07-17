<?php

namespace TeyvatPS\data\item;

class Material
{
    private int $itemId;
    private int $stackLimit;

    public function __construct(int $itemId, int $stackLimit)
    {
        $this->itemId = $itemId;
        $this->stackLimit = $stackLimit;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getStackLimit(): int
    {
        return $this->stackLimit;
    }
}

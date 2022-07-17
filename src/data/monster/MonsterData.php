<?php

namespace TeyvatPS\data\monster;

class MonsterData
{
    private int $id;
    private int $level = 1;

    private string $jsonName;
    private string $ai;

    private array $affix;
    private array $equips;

    public function __construct(
        int $id,
        string $jsonName,
        array $equips = [],
        array $affix = []
    ) {
        $this->id = $id;
        $this->jsonName = $jsonName;
        $this->equips = $equips;
        $this->affix = $affix;
    }

    public function getJsonName(): string
    {
        return $this->jsonName;
    }

    public function getAffix(): array
    {
        return $this->affix;
    }

    public function getEquips(): array
    {
        return $this->equips;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}

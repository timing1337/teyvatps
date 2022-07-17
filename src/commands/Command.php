<?php

namespace TeyvatPS\commands;

use TeyvatPS\game\Player;

abstract class Command
{
    private string $name;
    private string $description;
    private string $usage;
    private array $aliases;

    public function __construct(
        string $name,
        string $description,
        string $usage,
        array $aliases = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->usage = $usage;
        $this->aliases = $aliases;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUsage(): string
    {
        return $this->usage;
    }

    abstract public function onRun(Player $player, array $arguments): void;
}

<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\game\Player;

class PositionCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "position",
            "Get your current position",
            "!position"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        $pos = $player->getPosition();
        $player->sendMessage("You are at {$pos->getX()} {$pos->getY()} {$pos->getZ()}");
    }
}

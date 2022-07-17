<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\game\Player;

class RejoinCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "rejoin",
            "Disconnect the client and rejoin the game",
            "!rejoin"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        $player->sendMessage("Disconnecting...");
        $player->disconnect();
    }
}

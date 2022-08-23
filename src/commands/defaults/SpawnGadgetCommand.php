<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\data\ExcelManager;
use TeyvatPS\game\entity\Gadget;
use TeyvatPS\game\Player;

class SpawnGadgetCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "spawngadget",
            "Spawning gadget",
            "!spawngadget <id>"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if (!isset($arguments[0]) || !is_numeric($arguments[0])) {
            $player->sendMessage($this->getUsage());

            return;
        }
        $pos = $player->getPosition();
        $pos->setY($pos->getY() + 1);
        $gadgetEntity = new Gadget(
            $arguments[0],
            $player->getSession()->getWorld(),
            $pos
        );
        $player->getSession()->getWorld()->addEntity($gadgetEntity);
        $player->sendMessage(
            "Spawned gadget " . ExcelManager::getGadgetName($arguments[0])
        );
    }
}

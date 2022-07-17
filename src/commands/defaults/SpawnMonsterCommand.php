<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\data\ExcelManager;
use TeyvatPS\data\monster\MonsterData;
use TeyvatPS\game\entity\Monster;
use TeyvatPS\game\Player;

class SpawnMonsterCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "spawnmonster",
            "Spawning monster",
            "spawnmonster <id> <level|default = 1>"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if (!isset($arguments[0]) || !is_numeric($arguments[0])) {
            $player->sendMessage($this->getUsage());

            return;
        }
        $monsterData = ExcelManager::getMonsterInfo((int)$arguments[0]);
        if ($monsterData === null) {
            $monsterData = new MonsterData(
                (int)$arguments[0],
                "Undefined",
                [],
                []
            );
        }
        if (isset($arguments[1]) && is_numeric($arguments[1])) {
            $monsterData->setLevel((int)$arguments[1]);
        }
        $gadgetEntity = new Monster(
            $monsterData,
            $player->getSession()->getWorld(),
            $player->getPosition()
        );
        $player->getSession()->getWorld()->addEntity($gadgetEntity);
        $player->sendMessage("Spawned monster " . $monsterData->getJsonName());
    }
}

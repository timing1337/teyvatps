<?php

namespace TeyvatPS\game\quests;

use TeyvatPS\commands\Command;
use TeyvatPS\game\Player;
use TeyvatPS\network\protocol\DataPacket;

class QuestCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "quest",
            "Add quest",
            "!quest <id> <mainid> <state>"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if(!isset($arguments[0])) {
            $player->sendMessage("missing argument ");
            return;
        }
        $id = (int) $arguments[0];
        $mainId = isset($arguments[1]) ? (int) $arguments[1] : floor($id / 100);
        $state = isset($arguments[2]) ? (int) $arguments[2] : 2;
        $questUpdateNotify = new \QuestListUpdateNotify();
        $questUpdateNotify->setQuestList([
            QuestManager::generateDefaultQuest($id, $mainId, $state)
        ]);

        $finishedParentQuestUpdateNotify = new \FinishedParentQuestUpdateNotify();
        $finishedParentQuestUpdateNotify->setParentQuestList([
            QuestManager::generateParentQuest($mainId, $state === 2 ? 0 : 1)
        ]);
        $player->getSession()->send(new DataPacket("QuestListUpdateNotify", $questUpdateNotify));
        $player->getSession()->send(new DataPacket("FinishedParentQuestUpdateNotify", $finishedParentQuestUpdateNotify));
        $player->sendMessage("Added quest {$id} in {$mainId}");
    }
}
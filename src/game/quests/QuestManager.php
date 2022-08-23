<?php

namespace TeyvatPS\game\quests;

use TeyvatPS\commands\CommandManager;

class QuestManager
{

    public static function init(){
        CommandManager::registerCommand(new QuestCommand());
    }

    public static function generateDefaultQuest(int $id, int $parent = null, int $state = 2): \Quest
    {
        if($parent === null) $parent = floor($id / 100);
        $quest = new \Quest();
        $quest->setQuestId($id);
        $quest->setState($state);
        $quest->setStartTime(time());
        $quest->setAcceptTime(time());
        $quest->setParentQuestId($parent);
        $quest->setFinishProgressList([0]);
        return $quest;
    }

    public static function generateParentQuest(int $id, int $state = 1): \ParentQuest
    {
        $quest = new \ParentQuest();
        $quest->setParentQuestId($id);
        $quest->setIsRandom(false);
        $quest->setParentQuestState($state); // 1 = yay, 0 = not yay
        $quest->setChildQuestList([]);
        $quest->setIsFinished(true);
        return $quest;
    }
}
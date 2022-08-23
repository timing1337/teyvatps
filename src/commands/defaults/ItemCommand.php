<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\Config;
use TeyvatPS\game\Player;
use TeyvatPS\network\protocol\DataPacket;

class ItemCommand extends Command
{

public function __construct()
    {
        parent::__construct(
            "item",
            "Add item",
            "!item <id> <count>"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if (!isset($arguments[0])) {
            $player->sendMessage("missing argument ");
            return;
        }
        $id = (int) $arguments[0];
        $count = isset($arguments[1]) ? (int) $arguments[1] : 1;
        $notify = new \StoreItemChangeNotify();
        $notify->setStoreType(\StoreType::STORE_TYPE_PACK);
        $notify->setItemList([
            (new \Item())->setItemId($id)->setMaterial((new \Material())->setCount($count))->setGuid((Config::getUid() << 32) + 10000)
        ]);
        $player->getSession()->send(new DataPacket("StoreItemChangeNotify", $notify));
        $player->sendMessage("Added item {$id} x {$count}");
    }

}
//PacketStoreItemChangeNotify
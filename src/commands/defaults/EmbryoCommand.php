<?php

namespace TeyvatPS\commands\defaults;

use TeyvatPS\commands\Command;
use TeyvatPS\game\Player;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\utils\AvatarUtils;

class EmbryoCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "embryo",
            "Add embryo to your entities",
            "!embryo <abilityId>"
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if(!isset($arguments[0])) {
            $player->sendMessage($this->getUsage());
            return;
        }
        $cur = $player->getAvatarManager()->getAvatarByGuid($player->getAvatarManager()->getCurAvatarGuid());
        $embryos = $cur->getEmbryos();
        $embryos[] = (new \AbilityEmbryo())->setAbilityId(count($embryos) + 1)->setAbilityNameHash(AvatarUtils::getAbilityHash($arguments[0]));
        $abilityChangeNotify = new \AbilityChangeNotify();
        $abilityChangeNotify->setEntityId($cur->getId());
        $controlBlock = new \AbilityControlBlock();
        $controlBlock->setAbilityEmbryoList($embryos);
        $abilityChangeNotify->setAbilityControlBlock($controlBlock);
        $player->getSession()->send(new DataPacket("AbilityChangeNotify", $abilityChangeNotify));
        $player->sendMessage("Added ${arguments[0]} to your embryos");
    }
}

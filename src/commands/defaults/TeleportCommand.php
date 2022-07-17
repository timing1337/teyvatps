<?php

namespace TeyvatPS\commands\defaults;

use EnterReason;
use EnterType;
use TeyvatPS\commands\Command;
use TeyvatPS\game\Player;
use TeyvatPS\math\Vector3;

class TeleportCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "teleport",
            "teleport",
            "tp <x> <y> <z> <sceneId|optional>",
            ['tp']
        );
    }

    public function onRun(Player $player, array $arguments): void
    {
        if (count($arguments) < 3) {
            $player->sendMessage($this->getUsage());

            return;
        }

        $curPos = $player->getPosition();

        if ($arguments[0] === "~") {
            $x = $curPos->getX();
        } else {
            $x = (int)$arguments[0];
        }

        if ($arguments[1] === "~") {
            $y = $curPos->getY();
        } else {
            $y = (int)$arguments[1];
        }

        if ($arguments[2] === "~") {
            $z = $curPos->getZ();
        } else {
            $z = (int)$arguments[2];
        }

        if (isset($arguments[3]) && is_numeric($arguments[3])) {
            $sceneId = (int)$arguments[3];
        } else {
            $sceneId = $player->getSceneId();
        }

        $player->sendMessage(
            "Teleported to " . $x . " " . $y . " " . $z . " " . $sceneId
        );
        $player->teleport(
            $sceneId,
            new Vector3($x, $y, $z),
            EnterType::ENTER_TYPE_JUMP,
            EnterReason::TRANS_POINT
        );
    }
}

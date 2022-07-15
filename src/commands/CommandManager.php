<?php

namespace TeyvatPS\commands;

use TeyvatPS\commands\defaults\ActivityCommand;
use TeyvatPS\data\PlayerData;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class CommandManager
{

    private static array $commandsMap = [];

    public static function init(): void{
        NetworkServer::registerProcessor(\PrivateChatReq::class, function(Session $session, \PrivateChatReq $req): \PrivateChatRsp{
            $context = $req->getText();
            if($context[0] === "!"){
                $context = substr($context, 1);
                $arguments = explode(" ", $context);
                $command = array_shift($arguments);
                if(self::getCommand($command) !== null){
                    $command = self::getCommand($command);
                    $command->onRun($session->getPlayer(), $arguments);
                }else{
                    $session->getPlayer()->sendMessage("Command not found. Are you sure it exists?");
                }
            }
            $rsp = (new \PrivateChatRsp());
            return $rsp;
        });

        NetworkServer::registerProcessor(\PullPrivateChatReq::class, function(Session $session, \PullPrivateChatReq $req): \PullPrivateChatRsp{
            $chatInfo = new \ChatInfo();
            $chatInfo->setUid(0);
            $chatInfo->setToUid(PlayerData::UID);
            $chatInfo->setText("<3");
            $chatInfo->setTime(time());
            $rsp = (new \PullPrivateChatRsp());
            $rsp->setChatInfo([$chatInfo]);
            return $rsp;
        });

        NetworkServer::registerProcessor(\PullRecentChatReq::class, function(Session $session, \PullRecentChatReq $req): \PullRecentChatRsp{
            $chatInfo = new \ChatInfo();
            $chatInfo->setUid(0);
            $chatInfo->setToUid(PlayerData::UID);
            $chatInfo->setText("<3");
            $chatInfo->setTime(time());
            $rsp = (new \PullRecentChatRsp());
            $rsp->setChatInfo([$chatInfo]);
            return $rsp;
        });
    }

    public static function registerCommand(Command $command): void
    {
        self::$commandsMap[$command->getName()] = $command;
    }

    public static function getCommand(string $command): ?Command
    {
        return self::$commandsMap[$command] ?? null;
    }
}
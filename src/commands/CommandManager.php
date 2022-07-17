<?php

namespace TeyvatPS\commands;

use ChatInfo;
use PrivateChatReq;
use PrivateChatRsp;
use PullPrivateChatReq;
use PullPrivateChatRsp;
use PullRecentChatReq;
use PullRecentChatRsp;
use TeyvatPS\commands\defaults\RejoinCommand;
use TeyvatPS\commands\defaults\SpawnGadgetCommand;
use TeyvatPS\commands\defaults\SpawnMonsterCommand;
use TeyvatPS\commands\defaults\TeleportCommand;
use TeyvatPS\Config;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class CommandManager
{
    private static array $commandsMap = [];

    public static function init(): void
    {
        self::registerCommand(new SpawnMonsterCommand());
        self::registerCommand(new SpawnGadgetCommand());
        self::registerCommand(new RejoinCommand());
        self::registerCommand(new TeleportCommand());
        NetworkServer::registerProcessor(
            PrivateChatReq::class,
            function (Session $session, PrivateChatReq $req): PrivateChatRsp {
                $context = $req->getText();
                if ($context[0] === Config::getCommandPrefix()) {
                    $context = substr($context, 1);
                    $arguments = explode(" ", $context);
                    $command = array_shift($arguments);
                    if (self::getCommand($command) !== null) {
                        $command = self::getCommand($command);
                        $command->onRun($session->getPlayer(), $arguments);
                    } else {
                        $session->getPlayer()->sendMessage(
                            "Command not found. Are you sure it exists?"
                        );
                    }
                }

                return (new PrivateChatRsp())->setRetcode(0);
            }
        );

        NetworkServer::registerProcessor(
            PullPrivateChatReq::class,
            function (
                Session $session,
                PullPrivateChatReq $req
            ): PullPrivateChatRsp {
                $chatInfo = new ChatInfo();
                $chatInfo->setUid(0);
                $chatInfo->setToUid(Config::getUid());
                $chatInfo->setText("<3");
                $chatInfo->setTime(time());
                $rsp = (new PullPrivateChatRsp());
                $rsp->setChatInfo([$chatInfo]);

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            PullRecentChatReq::class,
            function (
                Session $session,
                PullRecentChatReq $req
            ): PullRecentChatRsp {
                $chatInfo = new ChatInfo();
                $chatInfo->setUid(0);
                $chatInfo->setToUid(Config::getUid());
                $chatInfo->setText("<3");
                $chatInfo->setTime(time());
                $rsp = (new PullRecentChatRsp());
                $rsp->setChatInfo([$chatInfo]);

                return $rsp;
            }
        );
    }

    public static function registerCommand(Command $command): void
    {
        self::$commandsMap[$command->getName()] = $command;
        foreach ($command->getAliases() as $alias) { //ehe
            self::$commandsMap[$alias] = $command;
        }
    }

    public static function getCommand(string $command): ?Command
    {
        return self::$commandsMap[$command] ?? null;
    }
}

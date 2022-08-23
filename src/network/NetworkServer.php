<?php

namespace TeyvatPS\network;

use Closure;
use GetPlayerTokenRsp;
use Google\Protobuf\Internal\Message;
use labalityowo\Bytebuffer\Buffer;
use PingReq;
use PingRsp;
use React\Datagram\Factory;
use React\Datagram\Socket;
use TeyvatPS\commands\CommandManager;
use TeyvatPS\Config;
use TeyvatPS\game\activities\ActivityManager;
use TeyvatPS\game\quests\QuestManager;
use TeyvatPS\game\reputation\ReputationManager;
use TeyvatPS\game\shop\ShopManager;
use TeyvatPS\game\windy\WindyManager;
use TeyvatPS\managers\InventoryManager;
use TeyvatPS\managers\LoginManager;
use TeyvatPS\managers\PlayerManager;
use TeyvatPS\managers\SceneManager;
use TeyvatPS\managers\TeamManager;
use TeyvatPS\managers\UnionCmdManager;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\protocol\Handshake;
use TeyvatPS\utils\Logger;

class NetworkServer
{
    private static Buffer $recv;
    private static Socket $server;
    /**
     * @var Closure[][]
     */
    private static array $processors = [];
    /**
     * @var Session[]
     */
    private static array $sessions = [];

    private static array $ignoredLog
        = [
            'EvtAiSyncSkillCdNotify',
            'EvtAiSyncCombatThreatInfoNotify',
            'UnionCmdNotify',
            'CombatInvocationsNotify',
            'PingReq',
            'PingRsp',
            'ClientAbilityChangeNotify',
            'AbilityInvocationsNotify',
            'ClientAbilityInitFinishNotify',
        ];

    public static function init(): void
    {
        self::$recv = Buffer::allocate(0x20000);

        $factory = new Factory;
        $factory->createServer(Config::getHost() . ":" . Config::getPort())
            ->then(function (Socket $server) {
                $server->on('message', [self::class, 'onReceived']);
                self::$server = $server;
            });

        self::registerProcessor(
            PingReq::class,
            function (Session $session, PingReq $req): PingRsp {
                return (new PingRsp)->setClientTime($req->getClientTime());
            }
        );

        LoginManager::init();
        SceneManager::init();
        PlayerManager::init();
        UnionCmdManager::init();
        InventoryManager::init();
        TeamManager::init();
        CommandManager::init();
        QuestManager::init();
    }

    public static function registerProcessor(
        string $class,
        Closure $processor
    ): void
    {
        self::$processors[$class][] = $processor;
    }

    public static function onReceived(
        string $message,
        string $address,
        Socket $socket
    ): void
    {
        $buffer = Buffer::new($message);
        $recvHandshake = new Handshake;
        [$ip, $port] = explode(':', $address);
        $port = (int)$port; //kek
        if ($recvHandshake->decode($buffer)) {
            if ($recvHandshake->start === Handshake::CONNECT_START
                && $recvHandshake->end === Handshake::CONNECT_END
                && $recvHandshake->data === Handshake::CONNECT_DATA
            ) {
                Logger::log(
                    'Received handshake connection from ' . Logger::YELLOW
                    . $address
                );
                $conv = 0x96969696; //Hardcoded
                $token = 0x42424242;
                $sndHandshake = new Handshake;
                $sndHandshake->start = Handshake::ESTABLISH_START;
                $sndHandshake->param1 = $conv;
                $sndHandshake->param2 = $token;
                $sndHandshake->data = Handshake::CONNECT_DATA;
                $sndHandshake->end = Handshake::ESTABLISH_END;
                $socket->send($sndHandshake->encode()->toString(), $address);
                Logger::log(
                    'Initializing session for ' . Logger::YELLOW . $address
                    . " (CONV: $conv | TOKEN: $token)"
                );
                self::registerSession($ip, $port);
            }
        } else {
            $session = self::getSession($ip, $port);
            if (!$session instanceof Session) {
                $disconnect = new Handshake;
                $disconnect->start = Handshake::DISCONNECT_START;
                $disconnect->end = Handshake::DISCONNECT_END;
                $socket->send($disconnect->encode()->toString(), $address);

                return;
            }
            $read = $session->getKcp()->input($buffer);
            if ($read < 0) {
                Logger::log(
                    'Received malformed packet from ' . Logger::YELLOW
                    . $address
                );

                return;
            }
            foreach ($session->process() as $packet) {
                self::process($session, $packet);
            }
        }
    }

    public static function registerSession(string $ip, int $port): void
    {
        $session = new Session(
            $ip,
            $port,
            0x96969696,
            0x42424242
        );
        self::$sessions[implode(":", [$ip, $port])] = $session;
    }

    public static function getSession(string $ip, int $port): ?Session
    {
        return self::$sessions[implode(":", [$ip, $port])] ?? null;
    }

    public static function process(Session $session, DataPacket $packet): void
    {
        $processors = self::$processors[get_class($packet->data)] ?? null;
        if ($processors !== null) {
            if (!in_array(get_class($packet->data), self::$ignoredLog)) {
                Logger::log("Handling : " . get_class($packet->data));
            }
            foreach ($processors as $processor) {
                $response = ($processor)($session, $packet->data);
                if (is_array($response)) {
                    foreach ($response as $r) {
                        $session->send(new DataPacket(get_class($r), $r));
                    }
                } else {
                    if ($response instanceof Message) {
                        $session->send(
                            new DataPacket(get_class($response), $response)
                        );
                        if ($response instanceof GetPlayerTokenRsp) {
                            $session->setInitialized();
                        }
                    }
                }
            }
        } else {
            if (!in_array(get_class($packet->data), self::$ignoredLog)) {
                Logger::log("Unhandled : " . get_class($packet->data));
            }
        }
    }

    public static function getServer(): Socket
    {
        return self::$server;
    }

    public static function getSessions(): array
    {
        return self::$sessions;
    }

    public static function getRecv(): Buffer
    {
        return self::$recv;
    }

    public static function getIgnoredLog(): array
    {
        return self::$ignoredLog;
    }
}

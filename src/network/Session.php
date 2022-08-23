<?php

namespace TeyvatPS\network;

use labalityowo\Bytebuffer\Buffer;
use labalityowo\kcp\KCP;
use React\EventLoop\Loop;
use TeyvatPS\game\Player;
use TeyvatPS\game\World;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\protocol\Handshake;
use TeyvatPS\utils\Crypto;
use TeyvatPS\utils\Logger;

class Session
{
    private string $address;
    private int $port;
    private int $conv;
    private int $token;

    private KCP $kcp;
    private bool $isInitialized = false;
    private Player $player;
    private World $world;

    public function __construct(
        string $address,
        int $port,
        int $conv,
        int $token
    )
    {
        $this->address = $address;
        $this->port = $port;
        $this->conv = $conv;
        $this->token = $token;
        $this->kcp = new KCP(
            $this->conv,
            $this->token,
            function (Buffer $buffer): void {
                NetworkServer::getServer()->send(
                    $buffer->toString(),
                    implode(':', [$this->address, $this->port])
                );
            }
        );
        $this->kcp->setNodelay(true, 2, true);
        $this->kcp->setInterval(10);

        Loop::addPeriodicTimer(0.01, function () {
            $this->kcp->update(time() * 1000);
            $this->kcp->flush();
        });
    }

    public function send(DataPacket $packet): void
    {
        if (!in_array($packet->getName(), NetworkServer::getIgnoredLog())) {
            Logger::log(
                "Sent packet " . Logger::YELLOW . "{$packet->getName()}"
            );
        }
        $packet = $packet->encode();
        Crypto::xorBuffer($packet, $this->getKey());
        $this->kcp->send($packet);
    }

    public function getKey(): Buffer
    {
        if (!$this->isInitialized) {
            return Crypto::$dispatchKey;
        } else {
            return Crypto::$secretKey;
        }
    }

    public function isInitialized(): bool
    {
        return $this->isInitialized;
    }

    public function createPlayer(): void
    {
        $this->world = new World($this);
        $this->player = new Player($this);
    }

    public function disconnect(): void
    {
        $disconnect = new Handshake;
        $disconnect->start = Handshake::DISCONNECT_START;
        $disconnect->end = Handshake::DISCONNECT_END;
        NetworkServer::getServer()->send(
            $disconnect->encode()->toString(),
            implode(':', [$this->address, $this->port])
        );
    }

    public function process(): array
    {
        $packets = [];
        $buffer = NetworkServer::getRecv();
        while (true) {
            $read = $this->kcp->recv($buffer);
            if ($read < 0) {
                break;
            }
            $decrypted = $buffer->slice(0, $read);
            Crypto::xorBuffer($decrypted, $this->getKey());
            $packet = new DataPacket;
            if ($packet->decode($decrypted)) {
                $packets[] = $packet;
            }
        }
        return $packets;
    }

    public function isConnected(): bool
    {
        return !$this->kcp->isDeadLink();
    }

    public function setInitialized(bool $isInitialized = true): void
    {
        $this->isInitialized = $isInitialized;
    }

    public function getKcp(): KCP
    {
        return $this->kcp;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getWorld(): World
    {
        return $this->world;
    }
}

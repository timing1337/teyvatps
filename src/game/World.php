<?php

namespace TeyvatPS\game;

use ProtEntityType;
use SceneEntityAppearNotify;
use SceneEntityDisappearNotify;
use TeyvatPS\Config;
use TeyvatPS\data\props\DataProperties;
use TeyvatPS\game\entity\Entity;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\Session;
use VisionType;

class World
{
    private Session $session;

    private array $entities = [];

    private int $nextEntityId = 0;
    private int $nextGuid = 0;

    private int $mpLevelEntityId;

    private int $sceneId;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->mpLevelEntityId = $this->getNextEntityId(
            ProtEntityType::PROT_ENTITY_TYPE_MP_LEVEL
        );
    }

    public function getNextEntityId(int $protEntityType): int
    {
        return ($protEntityType << 24) + ++$this->nextEntityId;
    }

    public function getWorldLevel(): int
    {
        $level = $this->session->getPlayer()->getProp(
            DataProperties::PROP_PLAYER_LEVEL
        );

        return match (true) {
            $level <= 20 => 1,
            $level <= 25 => 2,
            $level <= 30 => 3,
            $level <= 35 => 4,
            $level <= 40 => 5,
            $level <= 45 => 6,
            $level <= 50 => 7,
            default => 8
        };
    }

    public function getNextGuid(): int
    {
        return (Config::getUid() << 32) + ++$this->nextGuid;
    }

    public function getSceneId(): int
    {
        return $this->sceneId;
    }

    public function setSceneId(int $sceneId): void
    {
        $this->sceneId = $sceneId;
    }

    public function addEntity(Entity $entity): void
    {
        $packet = new SceneEntityAppearNotify();
        $packet->setEntityList([$entity->getSceneEntityInfo()]);
        $packet->setAppearType(VisionType::VISION_TYPE_BORN);
        $this->session->send(
            new DataPacket('SceneEntityAppearNotify', $packet)
        );
        $this->entities[$entity->getId()] = $entity;
    }

    public function killEntity(Entity $entity): void
    {
        $packet = new SceneEntityDisappearNotify();
        $packet->setEntityList([$entity->getId()]);
        $packet->setDisappearType(VisionType::VISION_TYPE_DIE);
        $this->session->send(
            new DataPacket('SceneEntityAppearNotify', $packet)
        );
        unset($this->entities[$entity->getId()]);
    }

    public function getEntityById(int $id): ?Entity
    {
        return $this->entities[$id] ?? null;
    }

    public function getEntityByGuid(int $id): ?Entity
    {
        foreach ($this->entities as $entity) {
            if ($entity->getGuid() === $id) {
                return $entity;
            }
        }

        return null;
    }

    public function getMpLevelEntityId(): int
    {
        return $this->mpLevelEntityId;
    }
}

<?php

namespace TeyvatPS\game\entity;

use AbilitySyncStateInfo;
use EntityAuthorityInfo;
use EntityRendererChangedInfo;
use MotionInfo;
use MotionState;
use SceneEntityAiInfo;
use SceneEntityInfo;
use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;
use Vector;

class Entity
{
    protected int $id;
    protected int $guid;
    private World $world;
    private Vector3 $motion;
    private Vector3 $rotation;
    private Vector3 $speed;

    private int $state;

    public function __construct(
        World $world,
        Vector3 $motion,
        Vector3 $rotation = null,
        Vector3 $speed = null
    ) {
        $this->world = $world;
        $this->motion = $motion;
        $this->rotation = ($rotation === null ? new Vector3(0, 0, 0)
            : $rotation);
        $this->speed = ($speed === null ? new Vector3(0, 0, 0) : $speed);
        $this->state = MotionState::MOTION_STATE_STANDBY;
    }

    public function getSceneEntityInfo(): SceneEntityInfo
    {
        $sceneEntityInfo = new SceneEntityInfo();
        $sceneEntityInfo->setEntityId($this->getId());
        $sceneEntityInfo->setLifeState(1);
        $sceneEntityInfo->setMotionInfo(
            (new MotionInfo())->setPos($this->motion->toProto())->setRot(
                $this->rotation->toProto()
            )->setSpeed($this->speed->toProto())->setState($this->state)
        );
        $sceneEntityInfo->setAnimatorParaList([]);
        $entityAuthorityInfo = new EntityAuthorityInfo();
        $entityAuthorityInfo->setAiInfo(
            (new SceneEntityAiInfo())->setIsAiOpen(true)
        );
        $entityAuthorityInfo->setAbilityInfo(new AbilitySyncStateInfo());
        $entityAuthorityInfo->setBornPos(new Vector());
        $entityAuthorityInfo->setPoseParaList([]);
        $entityAuthorityInfo->setRendererChangedInfo(
            new EntityRendererChangedInfo()
        );

        $sceneEntityInfo->setEntityAuthorityInfo($entityAuthorityInfo);

        return $sceneEntityInfo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getMotion(): Vector3
    {
        return $this->motion;
    }

    public function setMotion(Vector3 $motion): void
    {
        $this->motion = $motion;
    }

    public function getRotation(): Vector3
    {
        return $this->rotation;
    }

    public function setRotation(Vector3 $rotation): void
    {
        $this->rotation = $rotation;
    }

    public function getSpeed(): Vector3
    {
        return $this->speed;
    }

    public function setSpeed(Vector3 $speed): void
    {
        $this->speed = $speed;
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function getGuid(): int
    {
        return $this->guid;
    }
}

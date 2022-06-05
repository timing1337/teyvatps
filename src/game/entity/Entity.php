<?php

namespace TeyvatPS\game\entity;

use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;

class Entity
{
    private World $world;

    protected int $id;
    protected int $guid;

    private Vector3 $motion;
    private Vector3 $rotation;
    private Vector3 $speed;

    private int $state;
    public function __construct(World $world, Vector3 $motion, Vector3 $rotation = null, Vector3 $speed = null)
    {
        $this->world = $world;
        $this->motion = $motion;
        $this->rotation = ($rotation === null ? new Vector3(0, 0, 0) : $rotation);
        $this->speed = ($speed === null ? new Vector3(0, 0, 0) : $speed);
        $this->state = \MotionState::MOTION_STATE_STANDBY;
    }

    public function getSceneEntityInfo(): \SceneEntityInfo
    {
        $sceneEntityInfo = new \SceneEntityInfo();
        $sceneEntityInfo->setEntityId($this->getId());
        $sceneEntityInfo->setLifeState(1);
        $sceneEntityInfo->setMotionInfo((new \MotionInfo())->setPos($this->motion->toProto())->setRot($this->rotation->toProto())->setSpeed($this->speed->toProto())->setState($this->state));
        $sceneEntityInfo->setAnimatorParaList([]);
        return $sceneEntityInfo;
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getGuid(): int
    {
        return $this->guid;
    }
}
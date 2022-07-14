<?php

namespace TeyvatPS\game\entity;

use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;

class Gadget extends Entity
{

    private int $gadgetId;

    public function __construct(int $gadgetId, World $world, Vector3 $motion, Vector3 $rotation = null, Vector3 $speed = null)
    {
        $this->id = $world->getNextEntityId(\ProtEntityType::PROT_ENTITY_TYPE_GADGET);
        $this->guid = $world->getNextGuid();
        $this->gadgetId = $gadgetId;
        parent::__construct($world, $motion, $rotation, $speed);
    }

    public function getSceneEntityInfo(): \SceneEntityInfo
    {
        $sceneEntityInfo = parent::getSceneEntityInfo();
        $sceneEntityInfo->setEntityType(\ProtEntityType::PROT_ENTITY_TYPE_GADGET);
        $sceneEntityInfo->setGadget((new \SceneGadgetInfo())->setGadgetId($this->gadgetId)->setAuthorityPeerId(1)->setIsEnableInteract(false)->setVehicleInfo((new \VehicleInfo())->setCurStamina(240)->setOwnerUid(100)));
        return $sceneEntityInfo;
    }
}
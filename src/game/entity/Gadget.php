<?php

namespace TeyvatPS\game\entity;

use ProtEntityType;
use SceneEntityInfo;
use SceneGadgetInfo;
use TeyvatPS\data\props\EntityProperties;
use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;
use VehicleInfo;

class Gadget extends Entity
{
    private int $gadgetId;

    public function __construct(
        int $gadgetId,
        World $world,
        Vector3 $motion,
        Vector3 $rotation = null,
        Vector3 $speed = null
    )
    {
        $this->id = $world->getNextEntityId(
            ProtEntityType::PROT_ENTITY_TYPE_GADGET
        );
        $this->guid = $world->getNextGuid();
        $this->gadgetId = $gadgetId;
        parent::__construct($world, $motion, $rotation, $speed);
    }

    public function getSceneEntityInfo(): SceneEntityInfo
    {
        $sceneEntityInfo = parent::getSceneEntityInfo();
        $sceneEntityInfo->setEntityType(
            ProtEntityType::PROT_ENTITY_TYPE_GADGET
        );
        $sceneEntityInfo->setGadget($this->getSceneGadgetInfo());
        $sceneEntityInfo->setPropList([
            EntityProperties::toPropPair(
                EntityProperties::PROP_LEVEL,
                1
            ),
        ]);

        return $sceneEntityInfo;
    }

    public function getSceneGadgetInfo(): SceneGadgetInfo
    {
        $sceneGadgetInfo = new SceneGadgetInfo;
        $sceneGadgetInfo->setGadgetId($this->gadgetId);
        $sceneGadgetInfo->setAuthorityPeerId(1);
        $sceneGadgetInfo->setIsEnableInteract(false);
        $sceneGadgetInfo->setVehicleInfo(
            (new VehicleInfo)->setCurStamina(240)->setOwnerUid(100)
        );

        return $sceneGadgetInfo;
    }
}

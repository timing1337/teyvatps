<?php

namespace TeyvatPS\game\entity;

use AbilitySyncStateInfo;
use MonsterBornType;
use ProtEntityType;
use SceneEntityInfo;
use SceneMonsterInfo;
use SceneWeaponInfo;
use TeyvatPS\data\monster\MonsterData;
use TeyvatPS\data\props\EntityProperties;
use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;

class Monster extends Entity
{
    private MonsterData $monsterData;

    /**
     * @var array<int, float>
     */
    private array $fightProps = [];


    public function __construct(
        MonsterData $monsterData,
        World $world,
        Vector3 $motion,
        Vector3 $rotation = null,
        Vector3 $speed = null
    ) {
        $this->id = $world->getNextEntityId(
            ProtEntityType::PROT_ENTITY_TYPE_MONSTER
        );
        $this->guid = $world->getNextGuid();
        $this->monsterData = $monsterData;
        parent::__construct($world, $motion, $rotation, $speed);
    }

    public function getSceneEntityInfo(): SceneEntityInfo
    {
        $sceneEntityInfo = parent::getSceneEntityInfo();
        $sceneEntityInfo->setEntityType(
            ProtEntityType::PROT_ENTITY_TYPE_MONSTER
        );
        $sceneEntityInfo->setMonster($this->getSceneMonsterInfo());
        $sceneEntityInfo->setPropList([
            EntityProperties::toPropPair(
                EntityProperties::PROP_LEVEL,
                $this->monsterData->getLevel()
            ),
        ]);
        $sceneEntityInfo->setFightPropList($this->fightProps);

        return $sceneEntityInfo;
    }

    public function getSceneMonsterInfo(): SceneMonsterInfo
    {
        $sceneMonsterInfo = new SceneMonsterInfo();
        $sceneMonsterInfo->setMonsterId($this->monsterData->getId());
        $sceneMonsterInfo->setAffixList($this->monsterData->getAffix());
        $sceneMonsterInfo->setAuthorityPeerId(1);
        $sceneMonsterInfo->setPoseId(0);
        $sceneMonsterInfo->setBlockId(3001);
        $sceneMonsterInfo->setBornType(
            MonsterBornType::MONSTER_BORN_TYPE_DEFAULT
        );
        $sceneMonsterInfo->setSpecialNameId(40);
        $equips = [];
        foreach ($this->monsterData->getEquips() as $equip) {
            $equips[] = (new SceneWeaponInfo())->setGadgetId($equip)
                ->setAbilityInfo(new AbilitySyncStateInfo())->setEntityId(
                    $this->getWorld()->getNextEntityId(
                        ProtEntityType::PROT_ENTITY_TYPE_WEAPON
                    )
                );
        }
        $sceneMonsterInfo->setWeaponList($equips);

        return $sceneMonsterInfo;
    }
}

<?php

namespace TeyvatPS\game\entity;

use TeyvatPS\data\ExcelManager;
use TeyvatPS\data\PlayerData;
use TeyvatPS\data\props\AvatarProperties;
use TeyvatPS\game\World;
use TeyvatPS\math\Vector3;
use TeyvatPS\utils\AvatarUtils;

class Avatar extends Entity
{
    private \AvatarInfo $avatarInfo;

    /**
     * @var array<int, float>
     */
    private array $fightProps = [];

    /**
     * @var array<int, \PropValue>
     */
    private array $props = [];

    /**
     * @var \AbilityEmbryo[]
     */
    private array $embryos = [];

    public function __construct(World $world, \AvatarInfo $avatarInfo, Vector3 $motion, Vector3 $rotation = null, Vector3 $speed = null)
    {
        $this->avatarInfo = $avatarInfo;
        $this->id = $world->getNextEntityId(\ProtEntityType::PROT_ENTITY_TYPE_AVATAR);
        $this->guid = $this->avatarInfo->getGuid();

        foreach ($this->avatarInfo->getPropMap() as $propId => $propValue) {
            $this->props[$propId] = $propValue;
        }

        foreach ($this->avatarInfo->getFightPropMap() as $propId => $propValue) {
            $this->fightProps[$propId] = $propValue;
        }

        $embryoId = 1;

        $defaults = [
            1 => 0x05FF9657,
            2 => 0x0797D262,
            3 => 0x0C7599F3,
            4 => 0x1DAA7B46,
            5 => 0x1EE50216,
            6 => 0x279C736A,
            7 => 0x31306655,
            8 => 0x3404DEA1,
            9 => 0x35A975DB,
            10 => 0x36BCE44F,
            11 => 0x3E8B0DC0,
            12 => 0x43732FB4,
            13 => 0x441D271F,
            14 => 0x540E3E8E,
            15 => 0x57E91C26,
            16 => 0x5D3EEA62,
            17 => 0x5E10F925,
            18 => 0x74BF7A58,
            19 => 0x8973B6B7,
            20 => 0x9E17FC49,
            21 => 0xB4BD9D18,
            22 => 0xB5F36BFE,
            23 => 0xB91C23F9,
            24 => 0xBC3037E5,
            25 => 0xC34FDBD9,
            26 => 0xC3B1A5BB,
            27 => 0xC92024F2,
            28 => 0xCC650F14,
            29 => 0xCC650F15,
            30 => 0xD6820468,
            31 => 0xE0CCEE0D,
            32 => 0xE46A6608,
            33 => 0xF338F895,
            34 => 0xF56F5546,
            35 => 0xF8B2753E,
            36 => 0xFD8E4031,
            37 => 0xFFC8EAB3,
        ];

        foreach ($defaults as $hash)
        {
            $this->embryos[] = (new \AbilityEmbryo())->setAbilityId($embryoId++)->setAbilityNameHash($hash);
        }

        foreach (ExcelManager::getEmbryosFromId($this->avatarInfo->getAvatarId()) as $embryo)
        {
            $this->embryos[] = (new \AbilityEmbryo())->setAbilityId($embryoId++)->setAbilityNameHash(AvatarUtils::getAbilityHash($embryo->getAbilityName()));
        }

        parent::__construct($world, $motion, $rotation, $speed);
    }

    public function getEmbryos(): array
    {
        return $this->embryos;
    }

    public function getAvatarInfo(): \AvatarInfo
    {
        return $this->avatarInfo;
    }

    public function getAvatarPropPair(int $prop): \PropPair
    {
        return (new \PropPair())->setType($prop)->setPropValue($this->props[$prop]);
    }

    public function getFightProp(int $prop): float
    {
        return $this->fightProps[$prop];
    }

    public function setFightProp(int $prop, float $value): void
    {
        $this->fightProps[$prop] = $value;
        $this->avatarInfo->setFightPropMap($this->fightProps);
    }

    public function getProp(int $prop): int
    {
        return $this->props[$prop]->getVal();
    }

    public function setProp(int $prop, int $value): void
    {
        $this->props[$prop]->setVal($value)->setIval($value);
        $this->avatarInfo->setPropMap($this->props);
    }

    public function getSceneAvatarInfo(): \SceneAvatarInfo
    {
        $sceneAvatarInfo = new \SceneAvatarInfo();
        $sceneAvatarInfo->setGuid($this->getGuid());
        $sceneAvatarInfo->setUid(PlayerData::UID);
        $sceneAvatarInfo->setWearingFlycloakId(140001);
        $sceneAvatarInfo->setInherentProudSkillList($this->avatarInfo->getInherentProudSkillList());
        $sceneAvatarInfo->setProudSkillExtraLevelMap($this->avatarInfo->getProudSkillExtraLevelMap());
        $sceneAvatarInfo->setSkillLevelMap($this->avatarInfo->getSkillLevelMap());
        $sceneAvatarInfo->setTalentIdList($this->avatarInfo->getTalentIdList());
        $sceneAvatarInfo->setCoreProudSkillLevel($this->avatarInfo->getCoreProudSkillLevel());
        $sceneAvatarInfo->setBornTime($this->avatarInfo->getBornTime());
        $sceneAvatarInfo->setSkillDepotId($this->avatarInfo->getSkillDepotId());
        $sceneAvatarInfo->setAvatarId($this->avatarInfo->getAvatarId());
        $sceneAvatarInfo->setPeerId(1);
        $sceneAvatarInfo->setEquipIdList([11406]);
        $sceneAvatarInfo->setWeapon((new \SceneWeaponInfo())->setEntityId(100664575)->setGuid(2785642601942876162)->setItemId(11406)->setLevel(90)->setPromoteLevel(6)->setGadgetId(50011406)->setAbilityInfo(new \AbilitySyncStateInfo()));
        $sceneAvatarInfo->setTeamResonanceList($this->avatarInfo->getTeamResonanceList());
        //$sceneAvatarInfo->setExcelInfo($this->avatarInfo->getExcelInfo());
        return $sceneAvatarInfo;
    }

    public function getSceneEntityInfo(): \SceneEntityInfo
    {
        $sceneEntityInfo = parent::getSceneEntityInfo();
        $sceneEntityInfo->setEntityType(\ProtEntityType::PROT_ENTITY_TYPE_AVATAR);
        $sceneEntityInfo->setPropList([
            $this->getAvatarPropPair(AvatarProperties::PROP_LEVEL)
        ]);
        $fightProp = [];
        foreach ($this->fightProps as $key => $value) {
            $fightProp[] = (new \FightPropPair())->setPropValue($value)->setPropType($key);
        }
        $sceneEntityInfo->setFightPropList($fightProp);
        $sceneEntityInfo->setAvatar($this->getSceneAvatarInfo());
        return $sceneEntityInfo;
    }

    public function getSceneTeamAvatar(bool $isPlayerCurAvatar = true): \SceneTeamAvatar
    {
        $sceneTeamAvatar = new \SceneTeamAvatar();
        $sceneTeamAvatar->setSceneId($this->getWorld()->getSceneId());
        $sceneTeamAvatar->setPlayerUid(PlayerData::UID);
        $sceneTeamAvatar->setAvatarGuid($this->getGuid());
        $sceneTeamAvatar->setEntityId($this->getId());
        $sceneTeamAvatar->setAvatarAbilityInfo(new \AbilitySyncStateInfo());
        $sceneTeamAvatar->setWeaponGuid(2785642601942876162);
        $sceneTeamAvatar->setWeaponEntityId(100664575);
        $sceneTeamAvatar->setWeaponAbilityInfo(new \AbilitySyncStateInfo());
        $sceneTeamAvatar->setIsPlayerCurAvatar($isPlayerCurAvatar);
        $sceneTeamAvatar->setSceneEntityInfo($this->getSceneEntityInfo());
        $sceneTeamAvatar->setAbilityControlBlock((new \AbilityControlBlock())->setAbilityEmbryoList($this->embryos));
        return $sceneTeamAvatar;
    }

    public function getAvatarEnterSceneInfo(): \AvatarEnterSceneInfo
    {
        $avatarEnterSceneInfo = new \AvatarEnterSceneInfo();
        $avatarEnterSceneInfo->setAvatarGuid($this->getGuid());
        $avatarEnterSceneInfo->setAvatarEntityId($this->getId());
        $avatarEnterSceneInfo->setWeaponEntityId(100664575);
        $avatarEnterSceneInfo->setWeaponGuid(2785642601942876162);
        return $avatarEnterSceneInfo;
    }
}
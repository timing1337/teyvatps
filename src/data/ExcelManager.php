<?php

namespace TeyvatPS\data;

use TeyvatPS\Config;
use TeyvatPS\data\avatar\AbilityEmbryo;
use TeyvatPS\data\avatar\AvatarDepot;
use TeyvatPS\data\avatar\InherentProudSkillOpen;
use TeyvatPS\data\props\AvatarProperties;
use TeyvatPS\data\props\FightProperties;
use TeyvatPS\utils\AvatarUtils;

class ExcelManager
{
    private static array $idToName = [];
    /**
     * @var \AvatarInfo[]
     */
    private static array $avatars = [];

    /**
     * @var AvatarDepot[]
     */
    private static array $depots = [];

    /**
     * @var AbilityEmbryo[][]
     */
    private static array $embryos = [];

    public static function init(): void
    {
        $avatarExcelConfigData = json_decode(file_get_contents(Config::GENSHIN_DATA . 'ExcelBinOutput/AvatarExcelConfigData.json'), true);
        $avatarSkillDepotExcelConfigData = json_decode(file_get_contents(Config::GENSHIN_DATA . 'ExcelBinOutput/AvatarSkillDepotExcelConfigData.json'), true);

        foreach (glob(Config::GENSHIN_DATA . "BinOutput/Avatar/*.json") as $file) {
            $name = basename($file, '.json');
            if (!str_contains($name, 'ConfigAvatar') || str_contains($name, 'Manekin') || str_contains($name, 'Nude') || str_contains($name, 'Test')){
                continue;
            }
            $name = explode("ConfigAvatar_", $name)[1];
            $json = json_decode(file_get_contents($file), true);
            if(!isset($json['abilities']))
            {
                continue;
            }
            $abilities = [];
            foreach ($json['abilities'] as $ability) {
                $abilities[] = new AbilityEmbryo($ability['abilityID'], $ability['abilityName'], $ability['abilityOverride']);
            }
            self::$embryos[$name] = $abilities;
        }

        foreach ($avatarSkillDepotExcelConfigData as $key => $depot) {
            if (!isset($depot["energySkill"])) {
                continue;
            }

            $skillMap = [
                $depot["skills"][0],
                $depot["skills"][1],
                $depot["energySkill"],
            ];

            $proundSkillOpens = [];
            foreach ($depot["inherentProudSkillOpens"] as $proudSkillOpen) {
                if (!isset($proudSkillOpen["proudSkillGroupId"])) {
                    continue;
                }
                $proundSkillOpens[] = new InherentProudSkillOpen($proudSkillOpen["proudSkillGroupId"], $proudSkillOpen["needAvatarPromoteLevel"] ?? 0);
            }
            self::$depots[$depot["id"]] = new AvatarDepot($skillMap, $depot["subSkills"], $proundSkillOpens, $depot['talents']);
        }

        foreach ($avatarExcelConfigData as $avatarConfig){
            if(!isset($avatarConfig["useType"])){
                continue;
            }
            if($avatarConfig["useType"] === "AVATAR_ABANDON" || $avatarConfig["useType"] === "AVATAR_SYNC_TEST") continue;
            if ($avatarConfig["candSkillDepotIds"] != []) {
                $depotId = 704;
            }else{
                $depotId = $avatarConfig["skillDepotId"];
            }
            $depot = self::$depots[$depotId];
            /*
            README: Welcome to model changer without actually touching Melon! Only works with playable chars (?)
            $excelInfo = new \AvatarExcelInfo();
            $excelInfo->setPrefabPathHash(AvatarUtils::getHashByPreSuf($avatarConfig["prefabPathHashPre"], $avatarConfig["prefabPathHashSuffix"]));
            $excelInfo->setPrefabPathRemoteHash(AvatarUtils::getHashByPreSuf($avatarConfig["prefabPathRemoteHashPre"], $avatarConfig["prefabPathRemoteHashSuffix"]));
            */
            $proudSkills = $depot->getDefaultProudSkillsMap();

            $avatar = new \AvatarInfo();
            $avatar->setAvatarId($avatarConfig['id']);
            $avatar->setAvatarType(1);
            $avatar->setBornTime(time());
            $avatar->setSkillDepotId($depotId);
            $avatar->setTalentIdList([]);
            $avatar->setPropMap(AvatarProperties::getAll());
            $avatar->setFightPropMap(FightProperties::getAll());
            $avatar->setFetterInfo((new \AvatarFetterInfo())->setExpLevel(1));
            $avatar->setEquipGuidList([2785642601942876162]);
            $avatar->setInherentProudSkillList(array_keys($proudSkills));
            $avatar->setSkillLevelMap($depot->getDefaultSkillMap());
            $avatar->setProudSkillExtraLevelMap($proudSkills);
            $avatar->setWearingFlycloakId(140001);
            $avatar->setLifeState(1);
            //$avatar->setExcelInfo($excelInfo);
            self::$avatars[$avatarConfig['id']] = $avatar;
            self::$idToName[$avatarConfig['id']] = explode('UI_AvatarIcon_', $avatarConfig["iconName"])[1];
        }
    }

    public static function getEmbryosFromId(int $id): array
    {
        if(!isset(self::$idToName[$id]))
        {
            throw new \Exception("No avatar with id $id");
        }
        return self::$embryos[self::$idToName[$id]] ?? [];
    }

    public static function getDepotFromId(int $id): ?AvatarDepot
    {
        return self::$depots[$id] ?? null;
    }

    public static function getAvatars(): array
    {
        return self::$avatars;
    }
}
<?php

namespace TeyvatPS\data;

use AvatarFetterInfo;
use AvatarInfo;
use TeyvatPS\data\avatar\AbilityEmbryo;
use TeyvatPS\data\avatar\AvatarDepot;
use TeyvatPS\data\avatar\InherentProudSkillOpen;
use TeyvatPS\data\item\Material;
use TeyvatPS\data\item\Weapon;
use TeyvatPS\data\monster\MonsterData;
use TeyvatPS\data\props\EntityProperties;
use TeyvatPS\data\props\FightProperties;
use TeyvatPS\FolderConstants;

class ExcelManager
{
    private static array $idToName = [];
    /**
     * @var AvatarInfo[]
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

    private static array $namecards = [];
    /**
     * @var Material[]
     */
    private static array $materials = [];

    private static array $gadgets = [];
    private static array $monsters = [];

    private static array $weapons = [];

    public static function init(): void
    {
        $avatarExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/AvatarExcelConfigData.json'
            ),
            true
        );
        $avatarSkillDepotExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/AvatarSkillDepotExcelConfigData.json'
            ),
            true
        );
        $materialExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/MaterialExcelConfigData.json'
            ),
            true
        );
        $gadgetExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/GadgetExcelConfigData.json'
            ),
            true
        );
        $monsterExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/MonsterExcelConfigData.json'
            ),
            true
        );

        $weaponExcelConfigData = json_decode(
            file_get_contents(
                FolderConstants::GENSHIN_DATA
                . 'ExcelBinOutput/WeaponExcelConfigData.json'
            ),
            true
        );

        foreach ($monsterExcelConfigData as $monsterConfig) {
            self::$monsters[$monsterConfig["id"]] = new MonsterData(
                $monsterConfig["id"],
                $monsterConfig["monsterName"],
                $monsterConfig["equips"],
                $monsterConfig["affix"]
            );
        }

        foreach ($gadgetExcelConfigData as $gadgetConfig) {
            self::$gadgets[$gadgetConfig['id']] = $gadgetConfig['jsonName'];
        }

        foreach ($materialExcelConfigData as $materialConfig) {
            switch ($materialConfig["itemType"]) {
                case "ITEM_MATERIAL":
                    if (!isset($materialConfig["materialType"])) {
                        break;
                    }

                    if ($materialConfig["materialType"]
                        == "MATERIAL_NAMECARD"
                    ) {
                        self::$namecards[] = $materialConfig["id"];
                        break;
                    }

                    self::$materials[] = new Material(
                        $materialConfig["id"],
                        $materialConfig["stackLimit"] ?? 10000
                    );
                    break;
            }
        }
        self::$materials[] = new Material(
            143,
            10000
        );
        self::$materials[] = new Material(
            144,
            10000
        );
        foreach (
            glob(FolderConstants::GENSHIN_DATA . "BinOutput/Avatar/*.json") as
            $file
        ) {
            $name = basename($file, '.json');
            if (!str_contains($name, 'ConfigAvatar')
                || str_contains(
                    $name,
                    'Manekin'
                )
                || str_contains(
                    $name,
                    'Nude'
                )
                || str_contains($name, 'Test')
            ) {
                continue;
            }
            $name = explode("ConfigAvatar_", $name)[1];
            $json = json_decode(file_get_contents($file), true);
            if (!isset($json['abilities'])) {
                continue;
            }
            $abilities = [];
            foreach ($json['abilities'] as $ability) {
                $abilities[] = new AbilityEmbryo(
                    $ability['abilityID'],
                    $ability['abilityName'],
                    $ability['abilityOverride']
                );
            }
            self::$embryos[$name] = $abilities;
        }

        foreach ($avatarSkillDepotExcelConfigData as $depot) {
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
                $proundSkillOpens[] = new InherentProudSkillOpen(
                    $proudSkillOpen["proudSkillGroupId"] * 100 + 1,
                    $proudSkillOpen["needAvatarPromoteLevel"] ?? 0
                );
            }
            self::$depots[$depot["id"]] = new AvatarDepot(
                $skillMap,
                $depot["subSkills"],
                $proundSkillOpens,
                $depot['talents']
            );
        }

        foreach ($avatarExcelConfigData as $avatarConfig) {
            if (!isset($avatarConfig["useType"])) {
                continue;
            }
            if ($avatarConfig["useType"] === "AVATAR_ABANDON"
                || $avatarConfig["useType"] === "AVATAR_SYNC_TEST"
            ) {
                continue;
            }
            if ($avatarConfig["candSkillDepotIds"] != []) {
                $depotId = 704;
            } else {
                $depotId = $avatarConfig["skillDepotId"];
            }
            $depot = self::$depots[$depotId];
            $proudSkills = $depot->getDefaultProudSkillsMap();
            $avatar = new AvatarInfo;
            $avatar->setAvatarId($avatarConfig['id']);
            $avatar->setAvatarType(1);
            $avatar->setBornTime(time());
            $avatar->setSkillDepotId($depotId);
            $avatar->setTalentIdList($depot->getTalentIds());
            $avatar->setPropMap(EntityProperties::getAll());
            $avatar->setFightPropMap(FightProperties::getAll());
            $avatar->setFetterInfo((new AvatarFetterInfo)->setExpLevel(1));
            $avatar->setEquipGuidList([2785642601942876162]);
            $avatar->setInherentProudSkillList(array_keys($proudSkills));
            $avatar->setSkillLevelMap($depot->getDefaultSkillMap());
            $avatar->setProudSkillExtraLevelMap($proudSkills);
            $avatar->setWearingFlycloakId(140001);
            $avatar->setLifeState(1);
            $avatar->setCoreProudSkillLevel(6);
            self::$avatars[$avatarConfig['id']] = $avatar;
            self::$idToName[$avatarConfig['id']] = explode(
                'UI_AvatarIcon_',
                $avatarConfig["iconName"]
            )[1];
        }
    }

    public static function getWeapons(): array
    {
        return self::$weapons;
    }

    public static function getWeaponFromGuid(int $guid): ?Weapon
    {
        return self::$weapons[$gsuid] ?? null;
    }

    public static function getEmbryosFromId(int $id): array
    {
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

    public static function getNamecards(): array
    {
        return self::$namecards;
    }

    public static function getMaterialFromId(int $id): ?Material
    {
        return self::$materials[$id] ?? null;
    }

    public static function getMaterials(): array
    {
        return self::$materials;
    }

    public static function getGadgets(): array
    {
        return self::$gadgets;
    }

    public static function getGadgetName(int $id): string
    {
        return self::$gadgets[$id] ?? "";
    }

    public static function getMonsters(): array
    {
        return self::$monsters;
    }

    public static function getMonsterInfo(int $id): ?MonsterData
    {
        return self::$monsters[$id] ?? null;
    }
}

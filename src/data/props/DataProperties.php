<?php

namespace TeyvatPS\data\props;

use PropValue;
use ReflectionClass;

class DataProperties
{
    public const PROP_LAST_CHANGE_AVATAR_TIME = 10001;

    public const PROP_MAX_SPRING_VOLUME = 10002;

    public const PROP_CUR_SPRING_VOLUME = 10003;

    public const PROP_IS_SPRING_AUTO_USE = 10004;

    public const PROP_SPRING_AUTO_USE_PERCENT = 10005;

    public const PROP_IS_FLYABLE = 10006;

    public const PROP_IS_WEATHER_LOCKED = 10007;

    public const PROP_IS_GAME_TIME_LOCKED = 10008;

    public const PROP_IS_TRANSFERABLE = 10009;

    public const PROP_MAX_STAMINA = 10010;

    public const PROP_CUR_PERSIST_STAMINA = 10011;

    public const PROP_CUR_TEMPORARY_STAMINA = 10012;

    public const PROP_PLAYER_LEVEL = 10013;

    public const PROP_PLAYER_EXP = 10014;

    public const PROP_PLAYER_HCOIN = 10015;

    public const PROP_PLAYER_SCOIN = 10016;

    public const PROP_PLAYER_MP_SETTING_TYPE = 10017;

    public const PROP_IS_MP_MODE_AVAILABLE = 10018;

    public const PROP_PLAYER_WORLD_LEVEL = 10019;

    public const PROP_PLAYER_RESIN = 10020;

    public const PROP_PLAYER_WAIT_SUB_HCOIN = 10022;

    public const PROP_PLAYER_WAIT_SUB_SCOIN = 10023;

    public const PROP_IS_ONLY_MP_WITH_PS_PLAYER = 10024;

    public const PROP_PLAYER_MCOIN = 10025;

    public const PROP_PLAYER_WAIT_SUB_MCOIN = 10026;

    public const PROP_PLAYER_LEGENDARY_KEY = 10027;

    public const PROP_IS_HAS_FIRST_SHARE = 10028;

    public const PROP_PLAYER_FORGE_POINT = 10029;

    public const PROP_CUR_CLIMATE_METER = 10035;

    public const PROP_CUR_CLIMATE_TYPE = 10036;

    public const PROP_CUR_CLIMATE_AREA_ID = 10037;

    public const PROP_CUR_CLIMATE_AREA_CLIMATE_TYPE = 10038;

    public const PROP_PLAYER_WORLD_LEVEL_LIMIT = 10039;

    public const PROP_PLAYER_WORLD_LEVEL_ADJUST_CD = 10040;

    public const PROP_PLAYER_LEGENDARY_DAILY_TASK_NUM = 10041;

    public const PROP_PLAYER_HOME_COIN = 10042;

    public const PROP_PLAYER_WAIT_SUB_HOME_COIN = 10043;

    public static function getAll(): array
    {
        $rel = new ReflectionClass(self::class);
        $props = [];
        foreach ($rel->getConstants() as $prop) {
            $props[$prop] = (new PropValue)->setType($prop)->setVal(0)
                ->setIval(0);
        }

        return $props;
    }
}

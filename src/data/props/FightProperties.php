<?php

namespace TeyvatPS\data\props;

class FightProperties
{

    public const FIGHT_PROP_NONE = 0;
    public const FIGHT_PROP_BASE_HP = 1;
    public const FIGHT_PROP_HP = 2;
    public const FIGHT_PROP_HP_PERCENT = 3;
    public const FIGHT_PROP_BASE_ATTACK = 4;
    public const FIGHT_PROP_ATTACK = 5;
    public const FIGHT_PROP_ATTACK_PERCENT = 6;
    public const FIGHT_PROP_BASE_DEFENSE = 7;
    public const FIGHT_PROP_DEFENSE = 8;
    public const FIGHT_PROP_DEFENSE_PERCENT = 9;
    public const FIGHT_PROP_BASE_SPEED = 10;
    public const FIGHT_PROP_SPEED_PERCENT = 11;
    public const FIGHT_PROP_HP_MP_PERCENT = 12;
    public const FIGHT_PROP_ATTACK_MP_PERCENT = 13;
    public const FIGHT_PROP_CRITICAL = 20;
    public const FIGHT_PROP_ANTI_CRITICAL = 21;
    public const FIGHT_PROP_CRITICAL_HURT = 22;
    public const FIGHT_PROP_CHARGE_EFFICIENCY = 23;
    public const FIGHT_PROP_ADD_HURT = 24;
    public const FIGHT_PROP_SUB_HURT = 25;
    public const FIGHT_PROP_HEAL_ADD = 26;
    public const FIGHT_PROP_HEALED_ADD = 27;
    public const FIGHT_PROP_ELEMENT_MASTERY = 28;
    public const FIGHT_PROP_PHYSICAL_SUB_HURT = 29;
    public const FIGHT_PROP_PHYSICAL_ADD_HURT = 30;
    public const FIGHT_PROP_DEFENCE_IGNORE_RATIO = 31;
    public const FIGHT_PROP_DEFENCE_IGNORE_DELTA = 32;
    public const FIGHT_PROP_FIRE_ADD_HURT = 40;
    public const FIGHT_PROP_ELEC_ADD_HURT = 41;
    public const FIGHT_PROP_WATER_ADD_HURT = 42;
    public const FIGHT_PROP_GRASS_ADD_HURT = 43;
    public const FIGHT_PROP_WIND_ADD_HURT = 44;
    public const FIGHT_PROP_ROCK_ADD_HURT = 45;
    public const FIGHT_PROP_ICE_ADD_HURT = 46;
    public const FIGHT_PROP_HIT_HEAD_ADD_HURT = 47;
    public const FIGHT_PROP_FIRE_SUB_HURT = 50;
    public const FIGHT_PROP_ELEC_SUB_HURT = 51;
    public const FIGHT_PROP_WATER_SUB_HURT = 52;
    public const FIGHT_PROP_GRASS_SUB_HURT = 53;
    public const FIGHT_PROP_WIND_SUB_HURT = 54;
    public const FIGHT_PROP_ROCK_SUB_HURT = 55;
    public const FIGHT_PROP_ICE_SUB_HURT = 56;
    public const FIGHT_PROP_EFFECT_HIT = 60;
    public const FIGHT_PROP_EFFECT_RESIST = 61;
    public const FIGHT_PROP_FREEZE_RESIST = 62;
    public const FIGHT_PROP_TORPOR_RESIST = 63;
    public const FIGHT_PROP_DIZZY_RESIST = 64;
    public const FIGHT_PROP_FREEZE_SHORTEN = 65;
    public const FIGHT_PROP_TORPOR_SHORTEN = 66;
    public const FIGHT_PROP_DIZZY_SHORTEN = 67;
    public const FIGHT_PROP_MAX_FIRE_ENERGY = 70;
    public const FIGHT_PROP_MAX_ELEC_ENERGY = 71;
    public const FIGHT_PROP_MAX_WATER_ENERGY = 72;
    public const FIGHT_PROP_MAX_GRASS_ENERGY = 73;
    public const FIGHT_PROP_MAX_WIND_ENERGY = 74;
    public const FIGHT_PROP_MAX_ICE_ENERGY = 75;
    public const FIGHT_PROP_MAX_ROCK_ENERGY = 76;
    public const FIGHT_PROP_SKILL_CD_MINUS_RATIO = 80;
    public const FIGHT_PROP_SHIELD_COST_MINUS_RATIO = 81;
    public const FIGHT_PROP_CUR_FIRE_ENERGY = 1000;
    public const FIGHT_PROP_CUR_ELEC_ENERGY = 1001;
    public const FIGHT_PROP_CUR_WATER_ENERGY = 1002;
    public const FIGHT_PROP_CUR_GRASS_ENERGY = 1003;
    public const FIGHT_PROP_CUR_WIND_ENERGY = 1004;
    public const FIGHT_PROP_CUR_ICE_ENERGY = 1005;
    public const FIGHT_PROP_CUR_ROCK_ENERGY = 1006;
    public const FIGHT_PROP_CUR_HP = 1010;
    public const FIGHT_PROP_MAX_HP = 2000;
    public const FIGHT_PROP_CUR_ATTACK = 2001;
    public const FIGHT_PROP_CUR_DEFENSE = 2002;
    public const FIGHT_PROP_CUR_SPEED = 2003;
    public const FIGHT_PROP_NONEXTRA_ATTACK = 3000;
    public const FIGHT_PROP_NONEXTRA_DEFENSE = 3001;
    public const FIGHT_PROP_NONEXTRA_CRITICAL = 3002;
    public const FIGHT_PROP_NONEXTRA_ANTI_CRITICAL = 3003;
    public const FIGHT_PROP_NONEXTRA_CRITICAL_HURT = 3004;
    public const FIGHT_PROP_NONEXTRA_CHARGE_EFFICIENCY = 3005;
    public const FIGHT_PROP_NONEXTRA_ELEMENT_MASTERY = 3006;
    public const FIGHT_PROP_NONEXTRA_PHYSICAL_SUB_HURT = 3007;
    public const FIGHT_PROP_NONEXTRA_FIRE_ADD_HURT = 3008;
    public const FIGHT_PROP_NONEXTRA_ELEC_ADD_HURT = 3009;
    public const FIGHT_PROP_NONEXTRA_WATER_ADD_HURT = 3010;
    public const FIGHT_PROP_NONEXTRA_GRASS_ADD_HURT = 3011;
    public const FIGHT_PROP_NONEXTRA_WIND_ADD_HURT = 3012;
    public const FIGHT_PROP_NONEXTRA_ROCK_ADD_HURT = 3013;
    public const FIGHT_PROP_NONEXTRA_ICE_ADD_HURT = 3014;
    public const FIGHT_PROP_NONEXTRA_FIRE_SUB_HURT = 3015;
    public const FIGHT_PROP_NONEXTRA_ELEC_SUB_HURT = 3016;
    public const FIGHT_PROP_NONEXTRA_WATER_SUB_HURT = 3017;
    public const FIGHT_PROP_NONEXTRA_GRASS_SUB_HURT = 3018;
    public const FIGHT_PROP_NONEXTRA_WIND_SUB_HURT = 3019;
    public const FIGHT_PROP_NONEXTRA_ROCK_SUB_HURT = 3020;
    public const FIGHT_PROP_NONEXTRA_ICE_SUB_HURT = 3021;
    public const FIGHT_PROP_NONEXTRA_SKILL_CD_MINUS_RATIO = 3022;
    public const FIGHT_PROP_NONEXTRA_SHIELD_COST_MINUS_RATIO = 3023;
    public const FIGHT_PROP_NONEXTRA_PHYSICAL_ADD_HURT = 3024;

    public static function getAll(): array
    {
        return [
            self::FIGHT_PROP_BASE_HP => 20000,
            self::FIGHT_PROP_MAX_HP => 20000,
            self::FIGHT_PROP_BASE_DEFENSE => 3000,
            self::FIGHT_PROP_BASE_ATTACK => 3000,
            self::FIGHT_PROP_CRITICAL => 1,
            self::FIGHT_PROP_CRITICAL_HURT => 2,
            self::FIGHT_PROP_CHARGE_EFFICIENCY => 2,
            self::FIGHT_PROP_ELEMENT_MASTERY => 200,
            self::FIGHT_PROP_MAX_ROCK_ENERGY => 1,
            self::FIGHT_PROP_MAX_ICE_ENERGY => 1,
            self::FIGHT_PROP_MAX_WATER_ENERGY => 1,
            self::FIGHT_PROP_MAX_FIRE_ENERGY => 1,
            self::FIGHT_PROP_MAX_ELEC_ENERGY => 1,
            self::FIGHT_PROP_MAX_GRASS_ENERGY => 1,
            self::FIGHT_PROP_MAX_WIND_ENERGY => 1,
            self::FIGHT_PROP_CUR_ROCK_ENERGY => 1,
            self::FIGHT_PROP_CUR_ICE_ENERGY => 1,
            self::FIGHT_PROP_CUR_WATER_ENERGY => 1,
            self::FIGHT_PROP_CUR_ELEC_ENERGY => 1,
            self::FIGHT_PROP_CUR_FIRE_ENERGY => 1,
            self::FIGHT_PROP_CUR_WIND_ENERGY => 1,
            self::FIGHT_PROP_CUR_GRASS_ENERGY => 1,
            self::FIGHT_PROP_CUR_HP => 20000,
            self::FIGHT_PROP_CUR_DEFENSE => 3000,
            self::FIGHT_PROP_CUR_ATTACK => 3000,
            self::FIGHT_PROP_SKILL_CD_MINUS_RATIO => 1,
            self::FIGHT_PROP_SPEED_PERCENT => 2
        ];
    }
}
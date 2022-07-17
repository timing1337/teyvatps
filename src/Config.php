<?php

namespace TeyvatPS;

use FriendOnlineState;
use ProfilePicture;
use SocialDetail;

class Config
{
    private static string $host;
    private static int $port;

    private static int $uid;
    private static string $name;
    private static int $level;
    private static int $exp;
    private static int $namecard;
    private static int $achivements;
    private static string $signature;
    private static ProfilePicture $avatarId;

    private static string $commandPrefix;

    public static function init(): void
    {
        $config = json_decode(
            file_get_contents(FolderConstants::DATA_FOLDER . 'psconfig.json'),
            true
        );
        self::$host = $config['host'];
        self::$port = $config['port'];

        self::$uid = $config['player']['uid'];
        self::$name = $config['player']['name'];
        self::$level = $config['player']['level'];
        self::$exp = $config['player']['exp'];
        self::$namecard = $config['player']['namecard'];
        self::$achivements = $config['player']['achivements'];
        self::$signature = $config['player']['signature'];
        self::$avatarId = new ProfilePicture();
        self::$avatarId->setAvatarId($config['player']['avatarId']);

        self::$commandPrefix = $config['commands']['prefix'];
    }

    public static function getPlayerSocialDetail(): SocialDetail
    {
        $social = new SocialDetail();
        $social->setUid(self::getUid());
        $social->setNickname(self::getName());
        $social->setLevel(self::getLevel());
        $social->setNameCardId(self::getNamecard());
        $social->setFinishAchievementNum(self::getAchivements());
        $social->setSignature(self::getSignature());
        $social->setProfilePicture(self::getAvatarId());
        $social->setOnlineState(FriendOnlineState::FRIEND_ONLINE_STATE_ONLINE);
        $social->setIsFriend(true);
        $social->setWorldLevel(8);
        $social->setIsMpModeAvailable(true);

        return $social;
    }

    public static function getUid(): int
    {
        return self::$uid;
    }

    public static function getName(): string
    {
        return self::$name;
    }

    public static function getLevel(): int
    {
        return self::$level;
    }

    public static function getNamecard(): int
    {
        return self::$namecard;
    }

    public static function getAchivements(): int
    {
        return self::$achivements;
    }

    public static function getSignature(): string
    {
        return self::$signature;
    }

    public static function getAvatarId(): ProfilePicture
    {
        return self::$avatarId;
    }

    public static function getCommandPrefix(): string
    {
        return self::$commandPrefix;
    }

    public static function getHost(): string
    {
        return self::$host;
    }

    public static function getPort(): int
    {
        return self::$port;
    }

    public static function getExp(): int
    {
        return self::$exp;
    }
}

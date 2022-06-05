<?php

namespace TeyvatPS\data;

class PlayerData
{

    public const UID = 100;
    public const NAME = "<color=red>labalityowo</color> @ <color=yellow>Teyvat</color><color=orange>PS</color>";
    public const LEVEL = 60;
    public const EXP = 0;
    public const NAMECARD = 210001;
    public const ACHIEVEMENTS_NUM = 696969;
    public const SIGNATURE = "<color=red>https://github.com/teyvatps/</color>";
    public const AVATAR_ID = 10000007;

    public static function getPlayerSocialDetail(): \SocialDetail
    {
        $social = new \SocialDetail();
        $social->setUid(self::UID);
        $social->setNickname(self::NAME);
        $social->setLevel(self::LEVEL);
        $social->setNameCardId(self::NAMECARD);
        $social->setFinishAchievementNum(self::ACHIEVEMENTS_NUM);
        $social->setSignature(self::SIGNATURE);
        $social->setProfilePicture((new \ProfilePicture())->setAvatarId(self::AVATAR_ID));
        $social->setOnlineState(\FriendOnlineState::FRIEND_ONLINE_STATE_ONLINE);
        $social->setIsFriend(true);
        $social->setWorldLevel(8);
        $social->setIsMpModeAvailable(true);
        return $social;
    }
}
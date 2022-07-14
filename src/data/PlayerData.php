<?php

namespace TeyvatPS\data;

class PlayerData
{

    public const UID = 100;
    public const NAME = "<color=#56c596>l</color><color=#55c79b>a</color><color=#55caa1>b</color><color=#54cca6>a</color><color=#53ceac>l</color><color=#53d0b2>i</color><color=#52d3b8>t</color><color=#51d5be>y</color><color=#50d7c5>o</color><color=#4fdacc>w</color><color=#4edcd3>o</color><color=#4ededa> </color><color=#4ddfe0>@</color><color=#4cdce3> </color><color=#4ad8e5>T</color><color=#49d5e7>e</color><color=#48d1ea>y</color><color=#47ccec>v</color><color=#46c8ee>a</color><color=#45c3f0>t</color><color=#43bef3>P</color><color=#42b9f5>S</color>";
    public const LEVEL = 60;
    public const EXP = 0;
    public const NAMECARD = 210001;
    public const ACHIEVEMENTS_NUM = 696969;
    public const SIGNATURE = "";
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
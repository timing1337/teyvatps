<?php

namespace TeyvatPS\managers;

use ChangeGameTimeReq;
use ChangeGameTimeRsp;
use FriendBrief;
use FriendOnlineState;
use GetPlayerFriendListReq;
use GetPlayerFriendListRsp;
use GetPlayerSocialDetailReq;
use GetPlayerSocialDetailRsp;
use PlatformType;
use ProfilePicture;
use TeyvatPS\Config;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class PlayerManager
{
    public static function init(): void
    {
        NetworkServer::registerProcessor(
            GetPlayerSocialDetailReq::class,
            function (
                Session $session,
                GetPlayerSocialDetailReq $request
            ): GetPlayerSocialDetailRsp {
                return (new GetPlayerSocialDetailRsp)->setDetailData(
                    Config::getPlayerSocialDetail()
                );
            }
        );

        NetworkServer::registerProcessor(
            ChangeGameTimeReq::class,
            function (
                Session $session,
                ChangeGameTimeReq $req
            ): ChangeGameTimeRsp {
                return (new ChangeGameTimeRsp)->setCurGameTime(
                    $req->getGameTime()
                )->setExtraDays($req->getExtraDays());
            }
        );

        NetworkServer::registerProcessor(
            GetPlayerFriendListReq::class,
            function (
                Session $session,
                GetPlayerFriendListReq $req
            ): GetPlayerFriendListRsp {
                $default = new FriendBrief;
                $default->setUid(69);
                $default->setNickname("Console");
                $default->setLevel(60);
                $default->setProfilePicture(
                    (new ProfilePicture)->setAvatarId(10000037)
                );
                $default->setWorldLevel(8);
                $default->setSignature("Console");
                $default->setLastActiveTime(time());
                $default->setNameCardId(210001);
                $default->setOnlineState(
                    FriendOnlineState::FRIEND_ONLINE_STATE_ONLINE
                );
                $default->setParam(1);
                $default->setIsGameSource(true);
                $default->setPlatformType(PlatformType::PLATFORM_TYPE_PC);

                return (new GetPlayerFriendListRsp)->setFriendList(
                    [$default]
                );
            }
        );

        NetworkServer::registerProcessor(\PlayerOfferingReq::class, function (Session $session, \PlayerOfferingReq $req) {
            $offering = new \PlayerOfferingData();
            $offering->setOfferingId($req->getOfferingId());
            $offering->setLevel(50);
            $offering->setTakenLevelRewordList([]);
            $offering->setIsNewMaxLevel(false);
            $offering->setIsFirstInteract(false);

            $rsp = new \PlayerOfferingRsp;
            $rsp->setOfferings([$offering]);

            $offeringDataNotify = new \PlayerOfferingDataNotify();
            $offeringDataNotify->setOfferings([$offering]);
            return [$offeringDataNotify, $rsp];
        });

        NetworkServer::registerProcessor(\NpcTalkReq::class, function (Session $session, \NpcTalkReq $req) {
            $rsp = new \NpcTalkRsp;
            $rsp->setEntityId($req->getEntityId());
            $rsp->setNpcEntityId($req->getNpcEntityId());
            $rsp->setCurTalkId($req->getTalkId());
            var_dump($req->getTalkId());
            return $rsp;
        });
    }
}

<?php

namespace TeyvatPS\managers;

use TeyvatPS\data\PlayerData;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class PlayerManager
{

    public static function init(): void
    {
        NetworkServer::registerProcessor(\GetPlayerSocialDetailReq::class, function (Session $session, \GetPlayerSocialDetailReq $request): \GetPlayerSocialDetailRsp
        {
            return (new \GetPlayerSocialDetailRsp())->setDetailData(PlayerData::getPlayerSocialDetail());
        });

        NetworkServer::registerProcessor(\ChangeGameTimeReq::class, function (Session $session, \ChangeGameTimeReq $req): \ChangeGameTimeRsp {
            return (new \ChangeGameTimeRsp())->setCurGameTime($req->getGameTime())->setExtraDays($req->getExtraDays());
        });
    }
}
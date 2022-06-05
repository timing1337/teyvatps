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

        NetworkServer::registerProcessor(\SetUpAvatarTeamReq::class, function (Session $session, \SetUpAvatarTeamReq $request): \SetUpAvatarTeamRsp
        {
            //Workaround for MapField not being able to act like an array
            $team = [];
            foreach ($request->getAvatarTeamGuidList() as $guid){
                $team[] = $guid;
            }

            $rsp = new \SetUpAvatarTeamRsp();
            $rsp->setAvatarTeamGuidList($team);
            $rsp->setCurAvatarGuid($request->getCurAvatarGuid());

            $session->getPlayer()->getAvatarManager()->setCurTeamIndex($request->getTeamId());
            $session->getPlayer()->getAvatarManager()->setTeam($request->getTeamId(), $team);
            $session->getPlayer()->getAvatarManager()->setCurAvatarGuid($request->getCurAvatarGuid());
            return $rsp;
        });

        NetworkServer::registerProcessor(\ChangeAvatarReq::class, function (Session $session, \ChangeAvatarReq $request): array
        {
            $session->getPlayer()->getAvatarManager()->setCurAvatarGuid($request->getGuid());
            $rsp = new \ChangeAvatarRsp();
            $rsp->setCurGuid($request->getGuid());
            $rsp->setSkillId($request->getSkillId());
            return [$rsp];
        });
    }

}
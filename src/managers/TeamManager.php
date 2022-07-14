<?php

namespace TeyvatPS\managers;

use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class TeamManager
{

    public static function init(): void
    {
        NetworkServer::registerProcessor(\SetUpAvatarTeamReq::class, function (Session $session, \SetUpAvatarTeamReq $request): \SetUpAvatarTeamRsp
        {
            //Workaround for MapField not being able to act like an array
            $team = [];
            foreach ($request->getAvatarTeamGuidList() as $guid){
                $team[] = $guid;
            }
            $session->getPlayer()->getAvatarManager()->setTeam($request->getTeamId(), $team);
            if($request->getCurAvatarGuid() !== $session->getPlayer()->getAvatarManager()->getCurAvatarGuid()){
                $session->getPlayer()->getAvatarManager()->setCurAvatarGuid($request->getCurAvatarGuid());
            }
            $rsp = new \SetUpAvatarTeamRsp();
            $rsp->setTeamId($request->getTeamId());
            $rsp->setAvatarTeamGuidList($team);
            $rsp->setCurAvatarGuid($request->getCurAvatarGuid());
            return $rsp;
        });

        NetworkServer::registerProcessor(\ChangeAvatarReq::class, function (Session $session, \ChangeAvatarReq $request): \ChangeAvatarRsp
        {
            $session->getPlayer()->getAvatarManager()->setCurAvatarGuid($request->getGuid());
            $rsp = new \ChangeAvatarRsp();
            $rsp->setCurGuid($request->getGuid());
            $rsp->setSkillId($request->getSkillId());
            return $rsp;
        });

        NetworkServer::registerProcessor(\ChooseCurAvatarTeamReq::class, function (Session $session, \ChooseCurAvatarTeamReq $req): \ChooseCurAvatarTeamRsp{
            $rsp = new \ChooseCurAvatarTeamRsp();
            $rsp->setCurTeamId($req->getTeamId());
            $session->getPlayer()->getAvatarManager()->setCurTeamIndex($req->getTeamId());
            $session->getPlayer()->getAvatarManager()->updateTeam();
            $session->getPlayer()->getAvatarManager()->setCurAvatarGuid($session->getPlayer()->getAvatarManager()->getTeam($req->getTeamId())[0]);
            return $rsp;
        });
    }
}
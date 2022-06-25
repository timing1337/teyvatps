<?php

namespace TeyvatPS\managers;

use TeyvatPS\data\PlayerData;
use TeyvatPS\data\props\DataProperties;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class SceneManager
{

    private static array $points = [];
    private static array $areas = [];

    public static function init(): void
    {
        for($i = 0; $i < 1000; $i++)
        {
            self::$points[] = $i;
        }

        for ($i = 0; $i < 9; $i++)
        {
            self::$areas[] = $i;
        }
        NetworkServer::registerProcessor(\EnterSceneReadyReq::class, function(Session $session, \EnterSceneReadyReq $request): array
        {
            $enterScenePeerNotify = new \EnterScenePeerNotify();
            $enterScenePeerNotify->setDestSceneId($session->getWorld()->getSceneId());
            $enterScenePeerNotify->setPeerId(1);
            $enterScenePeerNotify->setHostPeerId(1);
            $enterScenePeerNotify->setEnterSceneToken($request->getEnterSceneToken());

            $enterSceneReadyRsp = new \EnterSceneReadyRsp();
            $enterSceneReadyRsp->setEnterSceneToken($request->getEnterSceneToken());
            return [$enterScenePeerNotify, $enterSceneReadyRsp];
        });

        NetworkServer::registerProcessor(\GetScenePointReq::class, function(Session $session, \GetScenePointReq $request): \GetScenePointRsp
        {
            return (new \GetScenePointRsp())->setSceneId($request->getSceneId())->setUnlockedPointList(self::$points)->setUnlockAreaList(self::$areas);
        });

        NetworkServer::registerProcessor(\GetSceneAreaReq::class, function(Session $session, \GetSceneAreaReq $request): \GetSceneAreaRsp
        {
            return (new \GetSceneAreaRsp())->setSceneId($request->getSceneId())->setAreaIdList([1,2,3,4,5,6,7,8,9,10,11,12,13,14,17,18,19,100,101,102,103,200,210,300]);
        });

        NetworkServer::registerProcessor(\SceneInitFinishReq::class, function (Session $session, \SceneInitFinishReq $request): array
        {
            $worldDataNotify = new \WorldDataNotify();
            $worldDataNotify->setWorldPropMap(
                [
                    1 => (new \PropValue())->setIval($session->getWorld()->getWorldLevel())->setType(1),
                    2 => (new \PropValue())->setIval(0)->setType(2),
                ]
            );

            $sceneIds = [];
            for($i = 0; $i < 3000; $i++){
                $sceneIds[] = $i;
            }

            $worldSceneInfoListNotify = new \PlayerWorldSceneInfoListNotify();
            $worldSceneInfoListNotify->setInfoList([
                (new \PlayerWorldSceneInfo())->setSceneId(9)->setIsLocked(false)->setSceneTagIdList($sceneIds)
            ]);

            $onlinePlayerInfo = new \OnlinePlayerInfo();
            $onlinePlayerInfo->setUid(PlayerData::UID);
            $onlinePlayerInfo->setNickname(PlayerData::NAME);
            $onlinePlayerInfo->setPlayerLevel($session->getPlayer()->getProp(DataProperties::PROP_PLAYER_LEVEL));
            $onlinePlayerInfo->setCurPlayerNumInWorld(1);
            $onlinePlayerInfo->setProfilePicture((new \ProfilePicture())->setAvatarId(PlayerData::AVATAR_ID));

            $scenePlayerInfo = new \ScenePlayerInfo();
            $onlinePlayerInfo->setUid(PlayerData::UID);
            $onlinePlayerInfo->setNickname(PlayerData::NAME);
            $scenePlayerInfo->setPeerId(1);
            $scenePlayerInfo->setSceneId($session->getWorld()->getSceneId());
            $scenePlayerInfo->setOnlinePlayerInfo($onlinePlayerInfo);

            $worldPlayerInfoNotify = new \WorldPlayerInfoNotify();
            $worldPlayerInfoNotify->setPlayerUidList([PlayerData::UID]);
            $worldPlayerInfoNotify->setPlayerInfoList([$onlinePlayerInfo]);

            $scenePlayerInfoNotify = new \ScenePlayerInfoNotify();
            $scenePlayerInfoNotify->setPlayerInfoList([$scenePlayerInfo]);

            $playerGameTimeNotify = new \PlayerGameTimeNotify();
            $playerGameTimeNotify->setUid(PlayerData::UID);
            $playerGameTimeNotify->setGameTime(60);

            $sceneTimeNotify = new \SceneTimeNotify();
            $sceneTimeNotify->setSceneId($session->getWorld()->getSceneId());

            $sceneDataNotify = new \SceneDataNotify();
            $sceneDataNotify->setLevelConfigNameList(["Level_BigWorld"]);

            $hostPlayerNotify = new \HostPlayerNotify();
            $hostPlayerNotify->setHostUid(PlayerData::UID);
            $hostPlayerNotify->setHostPeerId(1);

            $avatarManager = $session->getPlayer()->getAvatarManager();

            $sceneTeamUpdateNotify = new \SceneTeamUpdateNotify();
            $sceneTeamUpdateNotify->setSceneTeamAvatarList(array_map(function (int $guid) use($avatarManager, $session): \SceneTeamAvatar
            {
                $avatar = $avatarManager->getAvatarByGuid($guid);
                $avatar->setMotion($session->getPlayer()->getPosition());
                return $avatar->getSceneTeamAvatar($guid === $avatarManager->getCurAvatarGuid());
            }, $avatarManager->getTeam($avatarManager->getCurTeamIndex())));

            $playerEnterSceneInfoNotify = new \PlayerEnterSceneInfoNotify();
            $playerEnterSceneInfoNotify->setCurAvatarEntityId($session->getWorld()->getNextEntityId(\ProtEntityType::PROT_ENTITY_TYPE_AVATAR));
            $playerEnterSceneInfoNotify->setEnterSceneToken($request->getEnterSceneToken());
            $playerEnterSceneInfoNotify->setAvatarEnterInfo([
                $avatarManager->getAvatarByGuid($avatarManager->getCurAvatarGuid())->getAvatarEnterSceneInfo()
            ]);

            $playerEnterSceneInfoNotify->setMpLevelEntityInfo((new \MPLevelEntityInfo())->setEntityId($session->getWorld()->getMpLevelEntityId())->setAuthorityPeerId(1));
            $playerEnterSceneInfoNotify->setTeamEnterInfo((new \TeamEnterSceneInfo())->setTeamEntityId($session->getWorld()->getNextEntityId(\ProtEntityType::PROT_ENTITY_TYPE_TEAM)));

            $rsp = new \SceneInitFinishRsp();
            $rsp->setEnterSceneToken($request->getEnterSceneToken());

            return [$worldDataNotify, $worldPlayerInfoNotify, $scenePlayerInfoNotify, $playerGameTimeNotify, $sceneTimeNotify, $sceneDataNotify, $hostPlayerNotify, $sceneTeamUpdateNotify, $playerEnterSceneInfoNotify, $worldSceneInfoListNotify, $rsp];
        });

        NetworkServer::registerProcessor(\EnterSceneDoneReq::class, function(Session $session, \EnterSceneDoneReq $request): \EnterSceneDoneRsp
        {
            $avatarManager = $session->getPlayer()->getAvatarManager();
            $session->getWorld()->addEntity($avatarManager->getAvatarByGuid($avatarManager->getCurAvatarGuid()));
            $rsp = new \EnterSceneDoneRsp();
            $rsp->setEnterSceneToken($request->getEnterSceneToken());
            return $rsp;
        });

        NetworkServer::registerProcessor(\PostEnterSceneReq::class, function (Session $session, \PostEnterSceneReq $request): \PostEnterSceneRsp
        {
            $rsp = new \PostEnterSceneRsp();
            $rsp->setEnterSceneToken($request->getEnterSceneToken());
            return $rsp;
        });

        NetworkServer::registerProcessor(\MarkMapReq::class, function (Session $session, \MarkMapReq $request): \MarkMapRsp
        {
            if($request->getMark() !== null)
            {
                $position = new Vector3($request->getMark()->getPos()->getX(), 300, $request->getMark()->getPos()->getZ());
                $session->getPlayer()->teleport($request->getMark()->getSceneId(), $position, \EnterType::ENTER_TYPE_JUMP, \EnterReason::TRANS_POINT);
            }
            $rsp = new \MarkMapRsp();
            $rsp->setMarkList([]);
            return $rsp;
        });
    }
}
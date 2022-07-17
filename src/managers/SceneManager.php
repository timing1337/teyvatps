<?php

namespace TeyvatPS\managers;

use EnterReason;
use EnterSceneDoneReq;
use EnterSceneDoneRsp;
use EnterScenePeerNotify;
use EnterSceneReadyReq;
use EnterSceneReadyRsp;
use EnterType;
use GetSceneAreaReq;
use GetSceneAreaRsp;
use GetScenePointReq;
use GetScenePointRsp;
use HostPlayerNotify;
use MarkMapReq;
use MarkMapRsp;
use MPLevelEntityInfo;
use OnlinePlayerInfo;
use PlayerEnterSceneInfoNotify;
use PlayerGameTimeNotify;
use PlayerWorldSceneInfo;
use PlayerWorldSceneInfoListNotify;
use PostEnterSceneReq;
use PostEnterSceneRsp;
use PropValue;
use ProtEntityType;
use SceneDataNotify;
use SceneInitFinishReq;
use SceneInitFinishRsp;
use ScenePlayerInfo;
use ScenePlayerInfoNotify;
use SceneTeamAvatar;
use SceneTeamUpdateNotify;
use SceneTimeNotify;
use TeamEnterSceneInfo;
use TeyvatPS\Config;
use TeyvatPS\data\props\DataProperties;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;
use WorldDataNotify;
use WorldPlayerInfoNotify;

class SceneManager
{
    private static array $points = [];
    private static array $areas = [];

    public static function init(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            self::$points[] = $i;
        }

        for ($i = 0; $i < 500; $i++) {
            self::$areas[] = $i;
        }
        NetworkServer::registerProcessor(
            EnterSceneReadyReq::class,
            function (Session $session, EnterSceneReadyReq $request): array {
                $enterScenePeerNotify = new EnterScenePeerNotify();
                $enterScenePeerNotify->setDestSceneId(
                    $session->getWorld()->getSceneId()
                );
                $enterScenePeerNotify->setPeerId(1);
                $enterScenePeerNotify->setHostPeerId(1);
                $enterScenePeerNotify->setEnterSceneToken(
                    $request->getEnterSceneToken()
                );

                $enterSceneReadyRsp = new EnterSceneReadyRsp();
                $enterSceneReadyRsp->setEnterSceneToken(
                    $request->getEnterSceneToken()
                );

                return [$enterScenePeerNotify, $enterSceneReadyRsp];
            }
        );

        NetworkServer::registerProcessor(
            GetScenePointReq::class,
            function (
                Session $session,
                GetScenePointReq $request
            ): GetScenePointRsp {
                return (new GetScenePointRsp())->setSceneId(
                    $request->getSceneId()
                )->setUnlockedPointList(self::$points)->setUnlockAreaList(
                    self::$areas
                );
            }
        );

        NetworkServer::registerProcessor(
            GetSceneAreaReq::class,
            function (
                Session $session,
                GetSceneAreaReq $request
            ): GetSceneAreaRsp {
                return (new GetSceneAreaRsp())->setSceneId(
                    $request->getSceneId()
                )->setAreaIdList(self::$areas);
            }
        );

        NetworkServer::registerProcessor(
            SceneInitFinishReq::class,
            function (Session $session, SceneInitFinishReq $request): array {
                $worldDataNotify = new WorldDataNotify();
                $worldDataNotify->setWorldPropMap(
                    [
                        1 => (new PropValue())->setIval(
                            $session->getWorld()->getWorldLevel()
                        )->setType(1),
                        2 => (new PropValue())->setIval(0)->setType(2),
                    ]
                );

                $sceneIds = [];
                for ($i = 0; $i < 3000; $i++) {
                    $sceneIds[] = $i;
                }

                $worldSceneInfoListNotify = new PlayerWorldSceneInfoListNotify();
                $worldSceneInfoListNotify->setInfoList([
                    (new PlayerWorldSceneInfo())->setSceneId(9)->setIsLocked(
                        false
                    )->setSceneTagIdList($sceneIds),
                ]);

                $onlinePlayerInfo = new OnlinePlayerInfo();
                $onlinePlayerInfo->setUid(Config::getUid());
                $onlinePlayerInfo->setNickname(Config::getName());
                $onlinePlayerInfo->setPlayerLevel(
                    $session->getPlayer()->getProp(
                        DataProperties::PROP_PLAYER_LEVEL
                    )
                );
                $onlinePlayerInfo->setCurPlayerNumInWorld(1);
                $onlinePlayerInfo->setProfilePicture(Config::getAvatarId());

                $scenePlayerInfo = new ScenePlayerInfo();
                $onlinePlayerInfo->setUid(Config::getUid());
                $onlinePlayerInfo->setNickname(Config::getName());
                $scenePlayerInfo->setPeerId(1);
                $scenePlayerInfo->setSceneId(
                    $session->getWorld()->getSceneId()
                );
                $scenePlayerInfo->setOnlinePlayerInfo($onlinePlayerInfo);

                $worldPlayerInfoNotify = new WorldPlayerInfoNotify();
                $worldPlayerInfoNotify->setPlayerUidList([Config::getUid()]);
                $worldPlayerInfoNotify->setPlayerInfoList([$onlinePlayerInfo]);

                $scenePlayerInfoNotify = new ScenePlayerInfoNotify();
                $scenePlayerInfoNotify->setPlayerInfoList([$scenePlayerInfo]);

                $playerGameTimeNotify = new PlayerGameTimeNotify();
                $playerGameTimeNotify->setUid(Config::getUid());
                $playerGameTimeNotify->setGameTime(60);

                $sceneTimeNotify = new SceneTimeNotify();
                $sceneTimeNotify->setSceneId(
                    $session->getWorld()->getSceneId()
                );

                $sceneDataNotify = new SceneDataNotify();
                $sceneDataNotify->setLevelConfigNameList(["Level_BigWorld"]);

                $hostPlayerNotify = new HostPlayerNotify();
                $hostPlayerNotify->setHostUid(Config::getUid());
                $hostPlayerNotify->setHostPeerId(1);

                $avatarManager = $session->getPlayer()->getAvatarManager();

                $sceneTeamUpdateNotify = new SceneTeamUpdateNotify();
                $sceneTeamUpdateNotify->setSceneTeamAvatarList(
                    array_map(function (int $guid) use (
                        $avatarManager,
                        $session
                    ): SceneTeamAvatar {
                        $avatar = $avatarManager->getAvatarByGuid($guid);
                        $avatar->setMotion(
                            $session->getPlayer()->getPosition()
                        );

                        return $avatar->getSceneTeamAvatar(
                            $guid === $avatarManager->getCurAvatarGuid()
                        );
                    }, $avatarManager->getTeam(
                        $avatarManager->getCurTeamIndex()
                    ))
                );

                $playerEnterSceneInfoNotify = new PlayerEnterSceneInfoNotify();
                $playerEnterSceneInfoNotify->setCurAvatarEntityId(
                    $session->getWorld()->getNextEntityId(
                        ProtEntityType::PROT_ENTITY_TYPE_AVATAR
                    )
                );
                $playerEnterSceneInfoNotify->setEnterSceneToken(
                    $request->getEnterSceneToken()
                );
                $playerEnterSceneInfoNotify->setAvatarEnterInfo([
                    $avatarManager->getAvatarByGuid(
                        $avatarManager->getCurAvatarGuid()
                    )->getAvatarEnterSceneInfo(),
                ]);

                $playerEnterSceneInfoNotify->setMpLevelEntityInfo(
                    (new MPLevelEntityInfo())->setEntityId(
                        $session->getWorld()->getMpLevelEntityId()
                    )->setAuthorityPeerId(1)
                );
                $playerEnterSceneInfoNotify->setTeamEnterInfo(
                    (new TeamEnterSceneInfo())->setTeamEntityId(
                        $session->getWorld()->getNextEntityId(
                            ProtEntityType::PROT_ENTITY_TYPE_TEAM
                        )
                    )
                );

                $rsp = new SceneInitFinishRsp();
                $rsp->setEnterSceneToken($request->getEnterSceneToken());

                return [
                    $worldDataNotify,
                    $worldPlayerInfoNotify,
                    $scenePlayerInfoNotify,
                    $playerGameTimeNotify,
                    $sceneTimeNotify,
                    $sceneDataNotify,
                    $hostPlayerNotify,
                    $sceneTeamUpdateNotify,
                    $playerEnterSceneInfoNotify,
                    $worldSceneInfoListNotify,
                    $rsp,
                ];
            }
        );

        NetworkServer::registerProcessor(
            EnterSceneDoneReq::class,
            function (
                Session $session,
                EnterSceneDoneReq $request
            ): EnterSceneDoneRsp {
                $avatarManager = $session->getPlayer()->getAvatarManager();
                $session->getWorld()->addEntity(
                    $avatarManager->getAvatarByGuid(
                        $avatarManager->getCurAvatarGuid()
                    )
                );
                $rsp = new EnterSceneDoneRsp();
                $rsp->setEnterSceneToken($request->getEnterSceneToken());

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            PostEnterSceneReq::class,
            function (
                Session $session,
                PostEnterSceneReq $request
            ): PostEnterSceneRsp {
                $rsp = new PostEnterSceneRsp();
                $rsp->setEnterSceneToken($request->getEnterSceneToken());

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            MarkMapReq::class,
            function (Session $session, MarkMapReq $request): MarkMapRsp {
                if ($request->getMark() !== null) {
                    $position = new Vector3(
                        $request->getMark()->getPos()->getX(),
                        300,
                        $request->getMark()->getPos()->getZ()
                    );

                    $session->getPlayer()->teleport(
                        $request->getMark()->getSceneId(),
                        $position,
                        EnterType::ENTER_TYPE_JUMP,
                        EnterReason::TRANS_POINT
                    );
                }
                $rsp = new MarkMapRsp();
                $rsp->setMarkList([]);

                return $rsp;
            }
        );
    }
}

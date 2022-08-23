<?php

namespace TeyvatPS\managers;

use AiSyncInfo;
use ClientGadgetInfo;
use EnterReason;
use EnterSceneDoneReq;
use EnterSceneDoneRsp;
use EnterScenePeerNotify;
use EnterSceneReadyReq;
use EnterSceneReadyRsp;
use EnterType;
use EntityAiSyncNotify;
use EvtCreateGadgetNotify;
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
use TeyvatPS\game\entity\GadgetClient;
use TeyvatPS\game\quests\QuestManager;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;
use TeyvatPS\utils\AvatarUtils;
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
                $enterScenePeerNotify = new EnterScenePeerNotify;
                $enterScenePeerNotify->setDestSceneId(
                    $session->getWorld()->getSceneId()
                );
                $enterScenePeerNotify->setPeerId(1);
                $enterScenePeerNotify->setHostPeerId(1);
                $enterScenePeerNotify->setEnterSceneToken(
                    $request->getEnterSceneToken()
                );

                $enterSceneReadyRsp = new EnterSceneReadyRsp;
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
                return (new GetScenePointRsp)->setSceneId(
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
                $list = [];
                for($i = 0; $i < 5; $i++)
                {
                    $list[] = (new \CityInfo())->setLevel(10)->setCityId($i)->setCrystalNum(0);
                }
                return (new GetSceneAreaRsp)->setSceneId(
                    $request->getSceneId()
                )->setAreaIdList(self::$areas)->setCityInfoList($list);
            }
        );

        NetworkServer::registerProcessor(
            SceneInitFinishReq::class,
            function (Session $session, SceneInitFinishReq $request): array {
                $worldDataNotify = new WorldDataNotify;
                $worldDataNotify->setWorldPropMap(
                    [
                        1 => (new PropValue)->setIval(
                            $session->getWorld()->getWorldLevel()
                        )->setType(1),
                        2 => (new PropValue)->setIval(0)->setType(2),
                    ]
                );

                $sceneIds = [];
                for ($i = 0; $i < 3000; $i++) {
                    $sceneIds[] = $i;
                }

                $worldSceneInfoListNotify = new PlayerWorldSceneInfoListNotify;
                $worldSceneInfoListNotify->setInfoList([
                    (new PlayerWorldSceneInfo)->setSceneId(9)->setIsLocked(
                        false
                    )->setSceneTagIdList($sceneIds),
                ]);

                $onlinePlayerInfo = new OnlinePlayerInfo;
                $onlinePlayerInfo->setUid(Config::getUid());
                $onlinePlayerInfo->setNickname(Config::getName());
                $onlinePlayerInfo->setPlayerLevel(
                    $session->getPlayer()->getProp(
                        DataProperties::PROP_PLAYER_LEVEL
                    )
                );
                $onlinePlayerInfo->setCurPlayerNumInWorld(1);
                $onlinePlayerInfo->setProfilePicture(Config::getAvatarId());

                $scenePlayerInfo = new ScenePlayerInfo;
                $onlinePlayerInfo->setUid(Config::getUid());
                $onlinePlayerInfo->setNickname(Config::getName());
                $scenePlayerInfo->setPeerId(1);
                $scenePlayerInfo->setSceneId(
                    $session->getWorld()->getSceneId()
                );
                $scenePlayerInfo->setOnlinePlayerInfo($onlinePlayerInfo);

                $worldPlayerInfoNotify = new WorldPlayerInfoNotify;
                $worldPlayerInfoNotify->setPlayerUidList([Config::getUid()]);
                $worldPlayerInfoNotify->setPlayerInfoList([$onlinePlayerInfo]);

                $scenePlayerInfoNotify = new ScenePlayerInfoNotify;
                $scenePlayerInfoNotify->setPlayerInfoList([$scenePlayerInfo]);

                $playerGameTimeNotify = new PlayerGameTimeNotify;
                $playerGameTimeNotify->setUid(Config::getUid());
                $playerGameTimeNotify->setGameTime(60);

                $sceneTimeNotify = new SceneTimeNotify;
                $sceneTimeNotify->setSceneId(
                    $session->getWorld()->getSceneId()
                );

                $sceneDataNotify = new SceneDataNotify;
                $sceneDataNotify->setLevelConfigNameList(["Level_BigWorld"]);

                $hostPlayerNotify = new HostPlayerNotify;
                $hostPlayerNotify->setHostUid(Config::getUid());
                $hostPlayerNotify->setHostPeerId(1);

                $avatarManager = $session->getPlayer()->getAvatarManager();

                $sceneTeamUpdateNotify = new SceneTeamUpdateNotify;
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

                $playerEnterSceneInfoNotify = new PlayerEnterSceneInfoNotify;
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
                    (new MPLevelEntityInfo)->setEntityId(
                        $session->getWorld()->getMpLevelEntityId()
                    )->setAuthorityPeerId(1)
                );
                $playerEnterSceneInfoNotify->setTeamEnterInfo(
                    (new TeamEnterSceneInfo)->setTeamEntityId(
                        $session->getWorld()->getNextEntityId(
                            ProtEntityType::PROT_ENTITY_TYPE_TEAM
                        )
                    )
                );

                $questListNotify = new \QuestListNotify();
                $questListNotify->setQuestList(array_map(function ($questId){
                    return QuestManager::generateDefaultQuest($questId, null, 3);
                }, [7367401, 31101,38406,39604,101801,101609,102510,800311,1800027,200212,200711,200908,202102,1101505,800710,103106,46308,46618,48508,1010115,45406,49010,1100519,1200309,1020204,1050211,1110311,1101215,1011306,1102402,1120212,1103204,1104505,1111307,1012214,1112308,1201309,1200804,1201812,1202405,1203106,1202618,1204209,1203510,1102618,1203904,4000108,4000215,4000309,4000408,4000506,4111114,4111213,4111309,4111416,4121008,4121112,4121215,4121314,4121405,4131115,4131222,4131312,4141016,4141113,4141216,4141307,4001025,4001111,4001217,4001309,4002017,4002109,4002208,4001419,4001519,4002615,4002817,4002906,4005116,4003507,4003614,4003712,4005513,4005610,4005714,4005914,7303208,7303803,4006415,4006714,4007017,4007318,4007613,4007710]));
                var_dump($questListNotify->serializeToJsonString());
                $rsp = new SceneInitFinishRsp;
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
                    $questListNotify,
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
                $abilityChangeNotify = new \AbilityChangeNotify();

                $rsp = new EnterSceneDoneRsp;
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
                $rsp = new PostEnterSceneRsp;
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
                $rsp = new MarkMapRsp;
                $rsp->setMarkList([]);

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            EntityAiSyncNotify::class,
            function (Session $session, EntityAiSyncNotify $request): EntityAiSyncNotify {
                $rsp = new EntityAiSyncNotify;
                $infos = [];
                foreach ($request->getLocalAvatarAlertedMonsterList() as $monsterList) {
                    $infos[]
                        = (new AiSyncInfo)->setEntityId(33554491)->setHasPathToTarget(true);
                }
                $rsp->setInfoList($infos);

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(EvtCreateGadgetNotify::class,
            function (Session $session, EvtCreateGadgetNotify $request): array {
                $clientGadgetInfo = new ClientGadgetInfo;
                $clientGadgetInfo->setGuid($session->getWorld()->getNextGuid());
                $clientGadgetInfo->setAsyncLoad($request->getIsAsyncLoad());
                $clientGadgetInfo->setCampId($request->getCampId());
                $clientGadgetInfo->setCampType($request->getCampType());
                $clientGadgetInfo->setOwnerEntityId($request->getOwnerEntityId());
                $clientGadgetInfo->setTargetEntityId($request->getTargetEntityId());

                $gadgetEntity = new GadgetClient(
                    $request->getEntityId(),
                    $request->getConfigId(),
                    $clientGadgetInfo,
                    $session->getWorld(),
                    Vector3::fromProto($request->getInitPos()),
                    Vector3::fromProto($request->getInitEulerAngles())
                );
                $session->getWorld()->addEntity($gadgetEntity);

                return [];
            }
        );
        NetworkServer::registerProcessor(\EvtDestroyGadgetNotify::class,
            function (Session $session, \EvtDestroyGadgetNotify $request): array {
                $session->getWorld()->killEntityById($request->getEntityId());
                return [];
            }
        );

        NetworkServer::registerProcessor(\EvtEntityRenderersChangedNotify::class,
            function (Session $session, \EvtEntityRenderersChangedNotify $request): \EvtEntityRenderersChangedNotify {
                return $request;
            }
        );

    }
}

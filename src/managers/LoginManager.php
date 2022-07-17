<?php

namespace TeyvatPS\managers;

use AvatarDataNotify;
use EnterReason;
use EnterType;
use GetPlayerTokenReq;
use GetPlayerTokenRsp;
use Google\Protobuf\Internal\Message;
use Item;
use Material;
use OpenStateUpdateNotify;
use PlayerDataNotify;
use PlayerLoginReq;
use PlayerLoginRsp;
use PlayerStoreNotify;
use StoreType;
use StoreWeightLimitNotify;
use TeyvatPS\Config;
use TeyvatPS\data\ExcelManager;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;

class LoginManager
{
    public static function init(): void
    {
        NetworkServer::registerProcessor(
            GetPlayerTokenReq::class,
            function (Session $session, GetPlayerTokenReq $request): Message {
                if ($session->isInitialized()) {
                    $session->setInitialized(false);
                }
                $rsp = new GetPlayerTokenRsp();
                $rsp->setUid(Config::getUid());
                $rsp->setAccountType($request->getAccountType());
                $rsp->setAccountUid($request->getAccountUid());
                $rsp->setToken($request->getAccountToken());
                $rsp->setSecretKey("0");

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            PlayerLoginReq::class,
            function (Session $session, PlayerLoginReq $request): array {
                $session->createPlayer();

                $playerDataNotify = new PlayerDataNotify();
                $playerDataNotify->setNickName(Config::getName());
                $playerDataNotify->setServerTime(time() * 1000);
                $playerDataNotify->setRegionId(1); //?
                $playerDataNotify->setPropMap(
                    $session->getPlayer()->getPropMap()
                );

                $openStates = [];
                for ($i = 0; $i < 5000; $i++) {
                    $openStates[$i] = 1;
                }

                $openStatesNotify = new OpenStateUpdateNotify();
                $openStatesNotify->setOpenStateMap($openStates);

                $storeWeightLimitNotify = new StoreWeightLimitNotify();
                $storeWeightLimitNotify->setStoreType(
                    StoreType::STORE_TYPE_PACK
                );
                $storeWeightLimitNotify->setWeightLimit(30000);
                $storeWeightLimitNotify->setMaterialCountLimit(2000);
                $storeWeightLimitNotify->setWeaponCountLimit(2000);
                $storeWeightLimitNotify->setReliquaryCountLimit(1500);
                $storeWeightLimitNotify->setFurnitureCountLimit(2000);

                $playerStoreNotify = new PlayerStoreNotify();
                $playerStoreNotify->setStoreType(StoreType::STORE_TYPE_PACK);
                $playerStoreNotify->setWeightLimit(30000);
                $items = [];
                foreach (ExcelManager::getMaterials() as $material) {
                    $items[] = (new Item())->setItemId($material->getItemId())
                        ->setGuid($session->getWorld()->getNextGuid())
                        ->setMaterial(
                            (new Material())->setCount(
                                $material->getStackLimit()
                            )
                        );
                }
                $playerStoreNotify->setItemList($items);

                $avatarManager = $session->getPlayer()->getAvatarManager();

                $avatarDataNotify = new AvatarDataNotify();
                $avatarDataNotify->setAvatarList(
                    $avatarManager->getAvatarsInfo()
                );
                $avatarDataNotify->setAvatarTeamMap(
                    $avatarManager->toTeamMap()
                );
                $avatarDataNotify->setChooseAvatarGuid(
                    $avatarManager->getCurAvatarGuid()
                );
                $avatarDataNotify->setCurAvatarTeamId(
                    $avatarManager->getCurTeamIndex()
                );
                $avatarDataNotify->setOwnedFlycloakList([140001]);

                $session->getPlayer()->teleport(
                    3,
                    new Vector3(1, 300, -1),
                    EnterType::ENTER_TYPE_SELF,
                    EnterReason::LOGIN,
                    true
                );

                $rsp = new PlayerLoginRsp();
                $rsp->setGameBiz("hk4e_global");
                $rsp->setIsScOpen(false);

                return [
                    $playerDataNotify,
                    $openStatesNotify,
                    $storeWeightLimitNotify,
                    $playerStoreNotify,
                    $avatarDataNotify,
                    $rsp,
                ];
            }
        );
    }
}

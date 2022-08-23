<?php

namespace TeyvatPS\managers;

use AvatarDataNotify;
use EnterReason;
use EnterType;
use GetPlayerTokenReq;
use GetPlayerTokenRsp;
use Google\Protobuf\Internal\Message;
use Item;
use labalityowo\Bytebuffer\Buffer;
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
use TeyvatPS\utils\Crypto;

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
                $seed = 0;
                $rsp = new GetPlayerTokenRsp;
                $rsp->setUid(Config::getUid());
                $rsp->setAccountType($request->getAccountType());
                $rsp->setAccountUid($request->getAccountUid());
                $rsp->setToken($request->getAccountToken());
                $rsp->setSecretKey((string)$seed);

                //metadata patching...
                if ($request->getKeyId() > 0) {
                    $clientSeed = base64_decode($request->getClientSeed());
                    openssl_private_decrypt($clientSeed, $decryptedClientSeed, Crypto::$privateSigningKey, OPENSSL_PKCS1_PADDING);
                    $seedBytes = $seed ^ (unpack("J", $decryptedClientSeed)[1]);
                    $buffer = Buffer::new(pack("J", $seedBytes));
                    openssl_public_encrypt($buffer->toString(), $encryptedSeed, Crypto::$publicKey, OPENSSL_PKCS1_PADDING);
                    openssl_sign($buffer->toString(), $signature, Crypto::$privateSigningKey, OPENSSL_ALGO_SHA256);
                    $rsp->setEncryptedSeed(base64_encode($encryptedSeed));
                    $rsp->setSeedSignature(base64_encode($signature));
                }

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            PlayerLoginReq::class,
            function (Session $session, PlayerLoginReq $request): array {
                $session->createPlayer();
                $playerDataNotify = new PlayerDataNotify;
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

                $openStatesNotify = new OpenStateUpdateNotify;
                $openStatesNotify->setOpenStateMap($openStates);

                $storeWeightLimitNotify = new StoreWeightLimitNotify;
                $storeWeightLimitNotify->setStoreType(
                    StoreType::STORE_TYPE_PACK
                );
                $storeWeightLimitNotify->setWeightLimit(30000);
                $storeWeightLimitNotify->setMaterialCountLimit(2000);
                $storeWeightLimitNotify->setWeaponCountLimit(2000);
                $storeWeightLimitNotify->setReliquaryCountLimit(1500);
                $storeWeightLimitNotify->setFurnitureCountLimit(2000);

                $playerStoreNotify = new PlayerStoreNotify;
                $playerStoreNotify->setStoreType(StoreType::STORE_TYPE_PACK);
                $playerStoreNotify->setWeightLimit(30000);
                $items = [];
                foreach (ExcelManager::getMaterials() as $material) {
                    $items[] = (new Item)->setItemId($material->getItemId())
                        ->setGuid($session->getWorld()->getNextGuid())
                        ->setMaterial(
                            (new Material)->setCount(
                                $material->getStackLimit()
                            )
                        );
                }

                $playerStoreNotify->setItemList($items);

                $avatarManager = $session->getPlayer()->getAvatarManager();

                $avatarDataNotify = new AvatarDataNotify;
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
                    new Vector3(-387.3, 219.0, 2456.3),
                    EnterType::ENTER_TYPE_SELF,
                    EnterReason::LOGIN,
                    true
                );
                $rsp = new PlayerLoginRsp;
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

<?php

namespace TeyvatPS\game;

use ChatInfo;
use EnterReason;
use EnterType;
use PlayerEnterSceneNotify;
use PrivateChatNotify;
use PropValue;
use TeyvatPS\Config;
use TeyvatPS\data\props\DataProperties;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\Session;

class Player
{
    private Session $session;

    /**
     * @var PropValue[]
     */
    private array $propMap;

    private AvatarManager $avatarManager;
    private Vector3 $position;

    private int $widgetId;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->avatarManager = new AvatarManager($this->session);

        $this->propMap = DataProperties::getAll();
        $this->setProp(DataProperties::PROP_IS_SPRING_AUTO_USE, 1);
        $this->setProp(DataProperties::PROP_SPRING_AUTO_USE_PERCENT, 50);
        $this->setProp(DataProperties::PROP_IS_FLYABLE, 1);
        $this->setProp(DataProperties::PROP_IS_TRANSFERABLE, 1);
        $this->setProp(DataProperties::PROP_PLAYER_RESIN, 160);
        $this->setProp(DataProperties::PROP_CUR_PERSIST_STAMINA, 24000);
        $this->setProp(DataProperties::PROP_MAX_STAMINA, 24000);
        $this->setProp(DataProperties::PROP_PLAYER_LEVEL, Config::getLevel());
        $this->setProp(DataProperties::PROP_PLAYER_WORLD_LEVEL, 8);
        $this->setProp(DataProperties::PROP_PLAYER_HCOIN, 10000);
        $this->setProp(DataProperties::PROP_PLAYER_SCOIN, 10000);

    }

    public function setProp(int $prop, int $value): void
    {
        if(!isset($this->propMap[$prop])){
            $this->propMap[$prop] = (new PropValue())->setType($prop);
        }
        $this->propMap[$prop]->setVal($value)->setIval($value);
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getProp(int $prop): int
    {
        return (int)$this->propMap[$prop]->getIval();
    }

    public function getPropMap(): array
    {
        return $this->propMap;
    }

    public function getAvatarManager(): AvatarManager
    {
        return $this->avatarManager;
    }

    public function getPosition(): Vector3
    {
        return $this->position;
    }

    public function setPosition(Vector3 $position): void
    {
        $this->position = $position;
    }

    public function teleport(
        int $sceneId,
        Vector3 $position,
        int $type = EnterType::ENTER_TYPE_SELF,
        int $enterReason = EnterReason::LOGIN
    ): void
    {
        $playerEnterSceneNotify = new PlayerEnterSceneNotify;
        $playerEnterSceneNotify->setSceneId($sceneId);
        $playerEnterSceneNotify->setPos($position->toProto());
        $playerEnterSceneNotify->setSceneBeginTime(time() * 1000);
        $playerEnterSceneNotify->setType($type);
        $playerEnterSceneNotify->setEnterReason($enterReason);
        $playerEnterSceneNotify->setTargetUid(Config::getUid());
        $playerEnterSceneNotify->setEnterSceneToken(mt_rand(1000, 9999));
        $playerEnterSceneNotify->setWorldType(1);
        $playerEnterSceneNotify->setWorldLevel(8);
        $playerEnterSceneNotify->setSceneTransaction(
            $sceneId . "-" . Config::getUid() . "-" . time() . "-67458"
        );
        $sceneIds = [];
        for ($i = 0; $i < 3000; $i++) {
            $sceneIds[] = $i;
        }
        $playerEnterSceneNotify->setSceneTagIdList($sceneIds);
        if ($enterReason === EnterReason::LOGIN) {
            $playerEnterSceneNotify->setIsFirstLoginEnterScene(true);
        } else {
            $playerEnterSceneNotify->setPos($position->toProto());
            $playerEnterSceneNotify->setPrevSceneId(
                $this->session->getWorld()->getSceneId()
            );
        }
        $this->position = $position;
        $this->session->getWorld()->setSceneId($sceneId);
        $this->session->send(
            new DataPacket('PlayerEnterSceneNotify', $playerEnterSceneNotify)
        );
    }

    public function setSceneId(int $sceneId): void
    {
        $this->session->getWorld()->setSceneId($sceneId);
    }

    public function getSceneId(): int
    {
        return $this->session->getWorld()->getSceneId();
    }

    public function sendMessage(string $text, int $sender = 69): void
    {
        $notify = new PrivateChatNotify;
        $notify->setChatInfo(
            (new ChatInfo)->setText($text)->setUid($sender)->setToUid(
                Config::getUid()
            )
        );
        $this->session->send(new DataPacket('PrivateChatNotify', $notify));
    }

    public function getWidgetId(): int
    {
        return $this->widgetId;
    }

    public function setWidgetId(int $widgetId): void
    {
        $this->widgetId = $widgetId;
    }

    public function disconnect(): void
    {
        $this->session->disconnect();
    }
}

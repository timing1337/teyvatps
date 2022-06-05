<?php

namespace TeyvatPS\game;

use TeyvatPS\data\ExcelManager;
use TeyvatPS\game\entity\Avatar;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\Session;

class AvatarManager
{

    private Session $session;

    /**
     * @var Avatar[]
     */
    private array $avatars = [];
    /**
     * @var array<int, int[]>
     */
    private array $teams = [];

    private int $curTeamIndex = 1;
    private int $curAvatarGuid;

    public function __construct(Session $session){
        $this->session = $session;
        $avatars = ExcelManager::getAvatars();
        foreach ($avatars as $avatar)
        {
            $avatar->setGuid($this->session->getWorld()->getNextGuid());
            $this->avatars[] = new Avatar($this->session->getWorld(), $avatar, new Vector3(0, 0, 0));
        }

        $traveler = $this->getAvatarById(10000007);

        $this->teams = [
            1 => [
                $traveler->getGuid()
            ],
            2 => [],
            3 => [],
            4 => [],
        ];

        $this->curAvatarGuid = $traveler->getGuid();
    }

    public function getAvatarsInfo(): array
    {
        return array_map(function(Avatar $avatar){
            return $avatar->getAvatarInfo();
        }, $this->avatars);
    }

    public function getAvatarById(int $avatarId): ?Avatar
    {
        foreach ($this->avatars as $avatar){
            if($avatar->getAvatarInfo()->getAvatarId() === $avatarId) return $avatar;
        }
        return null;
    }

    public function getAvatarByGuid(int $guid): ?Avatar
    {
        foreach ($this->avatars as $avatar){
            if($avatar->getGuid() === $guid) return $avatar;
        }
        return null;
    }

    public function getCurTeamIndex(): int
    {
        return $this->curTeamIndex;
    }

    public function setCurTeamIndex(int $curTeamIndex): void
    {
        $this->curTeamIndex = $curTeamIndex;
    }

    public function getCurAvatarGuid(): int|string
    {
        return $this->curAvatarGuid;
    }

    public function setCurAvatarGuid(int|string $curAvatarGuid): void
    {
        $old = $this->getAvatarByGuid($this->curAvatarGuid);
        $new = $this->getAvatarByGuid($curAvatarGuid);

        $new->setState($old->getState());
        $new->setMotion($old->getMotion());
        $new->setRotation($old->getRotation());
        $new->setSpeed($old->getSpeed());

        $old->setState(\MotionState::MOTION_STATE_STANDBY);

        $this->session->getWorld()->killEntity($old);

        $this->curAvatarGuid = $curAvatarGuid;

        $this->session->getWorld()->addEntity($new);
    }

    public function getTeam(int $index): array
    {
        return $this->teams[$index];
    }

    public function setTeam(int $index, array $avatars, bool $requestTeamChange = true): void
    {
        $this->teams[$index] = $avatars;
        if($requestTeamChange) $this->updateTeam();
    }

    /**
     * @return \AvatarTeam[]
     */
    public function toTeamMap(): array
    {
        $teamsMap = [];
        for($i = 1; $i < 5; $i++)
        {
            $teamsMap[$i] = (new \AvatarTeam())->setAvatarGuidList($this->getTeam($i));
        }
        return $teamsMap;
    }

    public function updateTeam(): void
    {
        $curTeam = $this->getTeam($this->getCurTeamIndex());

        $avatarTeamUpdateNotify = new \AvatarTeamUpdateNotify();
        $avatarTeamUpdateNotify->setAvatarTeamMap($this->toTeamMap());

        $sceneTeamUpdateNotify = new \SceneTeamUpdateNotify();
        $sceneTeamUpdateNotify->setSceneTeamAvatarList(array_map(function (int $guid): \SceneTeamAvatar
        {
            $avatar = $this->getAvatarByGuid($guid);
            return $avatar->getSceneTeamAvatar($guid === $this->getCurAvatarGuid());
        }, $curTeam));

        $this->session->send(new DataPacket('AvatarTeamUpdateNotify', $avatarTeamUpdateNotify));
        $this->session->send(new DataPacket('SceneTeamUpdateNotify', $sceneTeamUpdateNotify));
    }
}
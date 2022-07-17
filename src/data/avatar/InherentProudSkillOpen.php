<?php

namespace TeyvatPS\data\avatar;

class InherentProudSkillOpen
{
    private int $proudSkillGroupId;
    private int $needAvatarPromoteLevel;

    public function __construct(
        int $proudSkillGroupId,
        int $needAvatarPromoteLevel
    ) {
        $this->proudSkillGroupId = $proudSkillGroupId;
        $this->needAvatarPromoteLevel = $needAvatarPromoteLevel;
    }

    public function getProudSkillGroupId(): int
    {
        return $this->proudSkillGroupId;
    }

    public function getNeedAvatarPromoteLevel(): int
    {
        return $this->needAvatarPromoteLevel;
    }
}

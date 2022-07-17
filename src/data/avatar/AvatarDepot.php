<?php

namespace TeyvatPS\data\avatar;

class AvatarDepot
{
    private array $skillMap;
    private array $subSkillMap;
    private array $talentIds;
    /**
     * @var InherentProudSkillOpen[]
     */
    private array $inherentProudSkillOpens;

    public function __construct(
        array $skillMap,
        array $subSkillMap,
        array $inherentProudSkillOpens,
        array $talentIds
    ) {
        $this->skillMap = $skillMap;
        $this->subSkillMap = $subSkillMap;
        $this->inherentProudSkillOpens = $inherentProudSkillOpens;
        $this->talentIds = $talentIds;
    }

    public function getSkillMap(): array
    {
        return $this->skillMap;
    }

    public function getTalentIds(): array
    {
        return $this->talentIds;
    }

    public function getInherentProudSkillOpens(): array
    {
        return $this->inherentProudSkillOpens;
    }

    public function getSubSkillMap(): array
    {
        return $this->subSkillMap;
    }

    public function getDefaultSkillMap(): array
    {
        $skillMap = [];
        foreach ($this->skillMap as $skill) {
            $skillMap[$skill] = 1;
        }

        return $skillMap;
    }

    public function getDefaultProudSkillsMap(): array
    {
        $skillMap = [];
        foreach ($this->inherentProudSkillOpens as $skill) {
            $skillMap[$skill->getProudSkillGroupId()] = 1;
        }

        return $skillMap;
    }
}

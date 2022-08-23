<?php

namespace TeyvatPS\data\avatar;

class AbilityEmbryo
{
    private string $abilityId;
    private string $abilityName;
    private string $abilityOverride;

    public function __construct(
        string $abilityId,
        string $abilityName,
        string $abilityOverride
    )
    {
        $this->abilityId = $abilityId;
        $this->abilityName = $abilityName;
        $this->abilityOverride = $abilityOverride;
    }

    public function getAbilityId(): string
    {
        return $this->abilityId;
    }

    public function getAbilityName(): string
    {
        return $this->abilityName;
    }

    public function getAbilityOverride(): string
    {
        return $this->abilityOverride;
    }
}

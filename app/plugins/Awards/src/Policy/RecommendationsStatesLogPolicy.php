<?php

declare(strict_types=1);

namespace Awards\Policy;

use App\Policy\BasePolicy;

/**
 * DomainPolicy policy
 */
class RecommendationsStatesLogPolicy extends BasePolicy
{
    protected string $REQUIRED_PERMISSION = "Can View Recommendations";
}
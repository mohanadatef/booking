<?php

namespace Modules\Stadium\Service;

use Modules\Stadium\Repositories\PitchRepository;
use Modules\Basic\Service\BasicService;

/**
 * Class PitchService
 * This class provides services related to stadium operations,
 * extending the functionality of BasicService.
 */
class PitchService extends BasicService
{
    protected PitchRepository $repo;

    /**
     * PitchService constructor.
     * Initializes the PitchService with a PitchRepository instance.
     *
     * @param PitchRepository $repository
     */
    public function __construct(PitchRepository $repository)
    {
        $this->repo = $repository;
    }

}


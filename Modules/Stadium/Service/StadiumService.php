<?php

namespace Modules\Stadium\Service;

use Modules\Stadium\Repositories\StadiumRepository;
use Modules\Basic\Service\BasicService;

/**
 * Class StadiumService
 * This class provides services related to stadium operations,
 * extending the functionality of BasicService.
 */
class StadiumService extends BasicService
{
    protected StadiumRepository $repo;

    /**
     * StadiumService constructor.
     * Initializes the StadiumService with a StadiumRepository instance.
     *
     * @param StadiumRepository $repository
     */
    public function __construct(StadiumRepository $repository)
    {
        $this->repo = $repository;
    }

}


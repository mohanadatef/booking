<?php

namespace Modules\Stadium\Service;

use Modules\Stadium\Repositories\BookingRepository;
use Modules\Basic\Service\BasicService;

/**
 * Class StadiumService
 * This class provides services related to stadium operations,
 * extending the functionality of BasicService.
 */
class StadiumService extends BasicService
{
    protected BookingRepository $repo;

    /**
     * StadiumService constructor.
     * Initializes the StadiumService with a StadiumRepository instance.
     *
     * @param BookingRepository $repository
     */
    public function __construct(BookingRepository $repository)
    {
        $this->repo = $repository;
    }

}


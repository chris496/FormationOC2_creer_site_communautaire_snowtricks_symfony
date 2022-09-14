<?php

namespace App\Services;

use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\Request;

class PagingService
{
    public function __construct(Request $request, TricksRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    public function paging($page, $limit)
    {
        $pages = (int)$this->request->query->get("page", $page);
        dd($pages);
        $paging = $this->repository->getPaginatedTricks($pages, $limit);

        return $paging;
    }
}

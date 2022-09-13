<?php

namespace App\Services;

class PagingService
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function paging($page, $limit)
    {
        
        $pages = (int)$this->request->query->get("page", $page);
        dd($pages);
        $paging = $repository->getPaginatedTricks($pages, $limit);

        return $paging;
    }
}

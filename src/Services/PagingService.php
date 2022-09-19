<?php

namespace App\Services;

use App\Repository\TricksRepository;
use App\Repository\CommentRepository;

class PagingService
{
    public function __construct(TricksRepository $tricksRepository, CommentRepository $commentRepository)
    {
        $this->tricksRepository = $tricksRepository;
        $this->commentRepository = $commentRepository;
    }

    public function pagingTricks($page, $limit, $pagingType, $request)
    {
        $pages = (int)$request->query->get("page", $page);
        $paging = $this->tricksRepository->$pagingType($pages, $limit);

        return $paging;
    }

    public function pagingComment($id, $page, $limit, $pagingType, $request)
    {
        $pages = (int)$request->query->get("page", $page);
        $paging = $this->commentRepository->$pagingType($id, $pages, $limit);

        return $paging;
    }
}

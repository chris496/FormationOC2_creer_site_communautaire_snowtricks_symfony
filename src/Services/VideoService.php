<?php

namespace App\Services;

class VideoService
{
    public function multi_video($urlVideo)
    {
        $url = $urlVideo;
        $explode = explode(',', $url);

        return $explode;
    }
}

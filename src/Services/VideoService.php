<?php

namespace App\Services;

class VideoService
{
    public function multi_video($urlVideo)
    {
        //dd($urlVideo);
        $var = $urlVideo;
        $tab = explode(',', $var);

        return $tab;
    }
}

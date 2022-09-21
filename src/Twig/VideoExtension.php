<?php

namespace App\Twig;

use App\Entity\Media;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class VideoExtension extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('video', [$this, 'getVideo']),
        ];
    }

    //public function getVideo(?string $url): ?string
    public function getVideo(?string $url)
    {

        //transforme url
        $urlUpdate = str_replace('http://', 'https://', $url);

        if (strpos($urlUpdate, 'youtube')) {
            $video = str_replace('www.youtube.com/watch?v=', 'www.youtube.com/embed/', $urlUpdate);
            $QueryPos = strpos($video, '&');
            $newURI = substr($video, 0, - (strlen($video) - $QueryPos));
            return $newURI;
        }

        if (strpos($urlUpdate, 'youtu.be')) {

            $video = str_replace('https://youtu.be/', 'https://www.youtube.com/embed/', $urlUpdate);
            return $video;
        }

        if (strpos($urlUpdate, 'vimeo')) {
            //$videoId = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $urlUpdate);
            $video = str_replace('https://vimeo.com/', '', $urlUpdate);
            $vimeoThumbs = 'http://vimeo.com/api/v2/video/' . $video . '.json';

            //curl request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $vimeoThumbs);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $curlData = curl_exec($curl);
            curl_close($curl);

            $vimeoThumbs = json_decode($curlData, true);
            dd($vimeoThumbs);
            $videoId = $vimeoThumbs[0]['url'];

            $videoId = str_replace('http://', 'https://', $videoId);

            return $videoId;
        }

        if (strpos($urlUpdate, 'dailymotion')) {
            $videoId = str_replace('https://www.dailymotion.com/video/', 'https://www.dailymotion.com/embed/video/', $urlUpdate);
            return $videoId;
        }

        if (strpos($urlUpdate, 'dai.ly')) {
            $videoId = str_replace('https://dai.ly/', 'https://www.dailymotion.com/embed/video/', $urlUpdate);
            return $videoId;
        }

        /*if($urlUpdate){
            return "https://www.youtube.com/embed/$urlUpdate";
        }
        if($url == "vimeo"){
            return "https://www.dailymotion.com/embed/video/$url";
        }
        if($url == "daily"){
            return "https://player.vimeo.com/video/$url";
        }
        return null;*/
    }
}


/*$assetUrl = str_replace('http://', 'https://', $assetUrl);
       if (strpos($assetUrl, 'youtube')) {
           $videoId = str_replace('https://www.youtube.com/watch?v=', '', $assetUrl);
           $videoThumb = 'https://i3.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
           return $videoThumb;
       }

       if (strpos($assetUrl, 'youtu.be')) {
           $videoId = str_replace('https://youtu.be/', '', $assetUrl);
           $videoThumb = 'https://i3.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
           return $videoThumb;
       }

       if (strpos($assetUrl, 'vimeo')) {
           $videoId = str_replace('https://vimeo.com/', '', $assetUrl);
           $vimeoThumbs = 'http://vimeo.com/api/v2/video/' . $videoId . '.json';

           //curl request
           $curl = curl_init();
           curl_setopt($curl, CURLOPT_URL, $vimeoThumbs);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           $curlData = curl_exec($curl);
           curl_close($curl);

           $vimeoThumbs = json_decode($curlData, true);
           $videoThumb = $vimeoThumbs[0]['thumbnail_medium'];
           $videoThumb = str_replace('http://', 'https://', $videoThumb);
           return $videoThumb;
       }

       if (strpos($assetUrl, 'dailymotion.com/embed')) {
           $videoId = str_replace('https://www.dailymotion.com/embed/video/', '', $assetUrl);
           $videoThumb = 'https://www.dailymotion.com/thumbnail/video/' . $videoId;
           return $videoThumb;
       }

       if (strpos($assetUrl, 'dailymotion')) {
           $videoId = str_replace('https://www.dailymotion.com/video/', '', $assetUrl);
           $videoThumb = 'https://www.dailymotion.com/thumbnail/video/' . $videoId;
           return $videoThumb;
       }

       $videoThumb = '/img/video-thumb.png';
       return $videoThumb;
   }*/

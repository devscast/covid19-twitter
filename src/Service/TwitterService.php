<?php

declare(strict_types=1);

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TwitterService
 * @package App\Service
 * @author bernard-ng <ngandubernard@gmail.com>
 */
class TwitterService
{
    private TwitterOAuth $connection;
    private Covid19Service $covid19;
    private PexelsService $pexels;

    /**
     * TwitterService constructor.
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function __construct(Covid19Service $covid19, PexelsService $pexels)
    {
        $this->connection = new TwitterOAuth(
            $_ENV['TWITTER_CONSUMER_KEY'],
            $_ENV['TWITTER_CONSUMER_SECRET'],
            $_ENV['TWITTER_ACCESS_TOKEN'],
            $_ENV['TWITTER_ACCESS_TOKEN_SECRET']
        );
        $this->covid19 = $covid19;
        $this->pexels = $pexels;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function post(): void
    {
        $media = $this->connection->upload('media/upload', ['media' => $this->pexels->get()]);
        $content = $this->covid19->getConfirmedCase();
        $parameters = [];

        if ($content) {
            $parameters['status'] = $this->covid19->getConfirmedCase();
        }

        if ($media) {
            $parameters['media_ids'] = $media->media_id_string;
        }

        if (!empty($parameters)) {
            $this->connection->post("statuses/update", $parameters);
        }
    }
}

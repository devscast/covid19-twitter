<?php

declare(strict_types=1);

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TwitterService
 * @package App\Service
 * @author bernard-ng <ngandubernard@gmail.com>
 */
class TwitterService
{
    private TwitterOAuth $connection;

    /**
     * TwitterService constructor.
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function __construct()
    {
        $this->connection = new TwitterOAuth(
            $_ENV['TWITTER_CONSUMER_KEY'],
            $_ENV['TWITTER_CONSUMER_SECRET'],
            $_ENV['TWITTER_ACCESS_TOKEN'],
            $_ENV['TWITTER_ACCESS_TOKEN_SECRET']
        );
    }

    /**
     * @param string $content
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function post(string $content): void
    {
        if ($content) {
            $this->connection->post("statuses/update", ["status" => $content]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PexelsService
 * @package App\Service
 * @author bernard-ng <ngandubernard@gmail.com>
 */
class PexelsService
{
    private const UPLOAD_URL = '/public/uploads/images/';
    private const BASE_URL = "https://api.pexels.com/v1/";
    private const KEYWORDS = ['covid19', 'covid+19', 'corona', 'face+mask+covid', 'vaccine'];
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private Filesystem $fs;
    private string $root;

    /**
     * PexelsService constructor.
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function __construct(KernelInterface $kernel, Filesystem $fs, LoggerInterface $logger)
    {
        $this->client = HttpClient::createForBaseUri(self::BASE_URL);
        $this->logger = $logger;
        $this->fs = $fs;
        $this->root = $kernel->getProjectDir() . self::UPLOAD_URL;

        if ($fs->exists($this->root)) {
            $fs->mkdir($this->root);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function get(): string
    {
        $key = self::KEYWORDS[array_rand(self::KEYWORDS)];
        $response = $this->client->request("GET", "search", [
            'headers' => ['Authorization' => $_ENV['PEXELS_KEY']],
            'query' => [
                'query' => $key,
                'per_page' => 80,
                'size' => 'small',
                'orientation' => 'landscape'
            ]
        ]);

        try {
            $data = $response->toArray()['photos'] ?: null;
            if ($data !== null) {
                $image = $data[array_rand($data)];
                $url = $image['src']['medium'] ?? $image['src']['original'];
                $this->fs->dumpFile($this->root . 'media.jpg', @file_get_contents($url));
                return $this->root . 'media.jpg';
            }
        } catch (
        ClientExceptionInterface |
        DecodingExceptionInterface |
        RedirectionException |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e
        ) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }


}

<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class CovidApiService
 * @author bernard-ng <ngandubernard@gmail.com>
 */
class Covid19Service
{
    public const COUNTRY_PATH = 'congo (kinshasa)--21.7587---4.0383';
    public const COUNTRY_ISO = 'COD';
    public const BASE_URL = 'https://covid19.mathdro.id/api/confirmed';
    private HttpClientInterface $client;

    /**
     * Covid19Service constructor.
     * @param HttpClientInterface $client
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return ?string
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function getConfirmedCase(): ?string
    {
        $data = ($this->client->request("GET", self::BASE_URL))->toArray();
        $congo = array_filter($data, function ($d) {
            $key = strtolower("{$d['countryRegion']}--{$d['long']}--{$d['lat']}");
            return $d['iso3'] === self::COUNTRY_ISO || $key === self::COUNTRY_PATH;
        });

        if ($congo) {
            $mentions = [
                '@OMSRDCONGO',
                '@actualitecd',
                '@StanysBujakera',
                '@politicocd',
                '@JacquesKyabula',
                '@OkondaBelise'
            ];
            $mention = $mentions[array_rand($mentions)];
            $stats = $data[array_key_first($congo)];
            
            $recovery = isset($stats['recovered']) ? "-> Guérisons : {$stats['recovered']}" : '';

            return <<< TWEET
                Situation actuelle du #COVID19 en #RDC
                -> Confirmés : {$stats['confirmed']}
                {$recovery}
                -> Morts : {$stats['deaths']}
                Mise à jour toutes les 3 heures

                #COVID19 #vaccine @MinSanteRDC {$mention}
            TWEET;
        }
        return null;
    }
}

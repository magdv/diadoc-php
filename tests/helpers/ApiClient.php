<?php

declare(strict_types=1);

namespace Test\helpers;

use DivineOmega\DOFileCache\DOFileCache;
use MagDv\Diadoc\DiadocApi;
use MagDv\Diadoc\Signer\OpensslSignerProvider;
use Test\enums\ConfigNames;

class ApiClient
{
    private DiadocApi $api;
    private ?string $token = null;
    private DOFileCache $cache;

    private function auth(): void
    {
        if (null === $this->token) {
            $token = $this->cache->get(ConfigNames::DIADOC_TOKEN_KEY);
            if (false === $token) {
                $this->setAuthToken();
            } else {
                $this->token = $token;
                $this->api->setToken($this->token);
            }
        }
    }

    private function setAuthToken(): void
    {
        $this->token = $this->api->authenticateLogin(
            getenv(ConfigNames::AUTH_LOGIN),
            getenv(ConfigNames::AUTH_PASSWORD)
        );
        $this->cache->set(ConfigNames::DIADOC_TOKEN_KEY, $this->token, strtotime('+ 11 hours'));
        $this->api->setToken($this->token);
    }


    /**
     * @return \MagDv\Diadoc\DiadocApi
     */
    public function getApi(): DiadocApi
    {
        $caFile = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'domain.crt';
        $certFile = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'domain.csr';
        $keyFile = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'domain.key';
        $signedProvider = new OpensslSignerProvider($caFile, $certFile, $keyFile);
        $this->api = new DiadocApi(
            getenv(ConfigNames::DD_AUTH),
            getenv(ConfigNames::DIADOC_URL),
            false,
            $signedProvider
        );
        $this->cache = Cache::getCache();
        $this->auth();

        return $this->api;
    }
}

<?php

declare(strict_types=1);

namespace Test\helpers;

use MagDv\Diadoc\DiadocApi;
use Test\enums\ConfigNames;

class ApiClient
{
    /**
     * @var DiadocApi
     */
    private $api;

    /**
     * @var null|string
     */
    private $token;
    /**
     * @var \DivineOmega\DOFileCache\DOFileCache
     */
    private $cache;

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
        $this->api = new DiadocApi(
            getenv(ConfigNames::DD_AUTH),
            getenv(ConfigNames::DIADOC_URL)
        );
        $this->cache = Cache::getCache();
        $this->auth();

        return $this->api;
    }
}

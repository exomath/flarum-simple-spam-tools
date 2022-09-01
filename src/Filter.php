<?php

namespace Luceos\Spam;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Foundation\Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class Filter implements ExtenderInterface
{
    public static array $acceptableDomains = [];
    public static ?int $userPostCount = null;
    public static ?int $userAge = null;
    public static ?int $moderatorUserId = null;

    public function allowLinksFromDomain(string $domain)
    {
        $scheme = parse_url($domain, PHP_URL_SCHEME) ?? 'http://';

        $domain = $scheme . Str::after($domain, $scheme);

        $domain = parse_url($domain, PHP_URL_HOST);

        static::$acceptableDomains[] = $domain;

        return $this;
    }

    public function allowLinksFromDomains(array $domains)
    {
        foreach ($domains as $domain) {
            $this->allowLinksFromDomain($domain);
        }

        return $this;
    }

    public function checkForUserUpToPostContribution(int $posts = 1)
    {
        static::$userPostCount = $posts;

        return $this;
    }

    public function checkForUserUpToHoursSinceSignUp(int $hours = 1)
    {
        static::$userAge = $hours;

        return $this;
    }

    public function moderateAsUser(int $userId)
    {
        static::$moderatorUserId = $userId;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {

    }

    public static function getAcceptableDomains(): array
    {
        /** @var Config $config */
        $config = resolve(Config::class);

        return static::$acceptableDomains + [$config->url()];
    }
}

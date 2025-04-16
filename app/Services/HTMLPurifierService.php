<?php

namespace App\Services;

class HTMLPurifierService
{
    public function purify(string $content): string
    {
        $config = \HTMLPurifier_Config::createDefault();

        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($content);
    }
}

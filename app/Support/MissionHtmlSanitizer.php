<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class MissionHtmlSanitizer
{
    public function sanitize(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('Cache.SerializerPath', storage_path('framework/cache/data'));
        $config->set(
            'HTML.Allowed',
            'p,br,strong,b,em,i,u,s,blockquote,code,pre,h2,h3,h4,ul,ol,li,'
            .'table,thead,tbody,tr,th,td,a[href|title]'
        );
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
        ]);

        return (new HTMLPurifier($config))->purify($html);
    }
}

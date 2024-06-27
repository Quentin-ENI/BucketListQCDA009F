<?php

namespace App\Service;

class CensuratorService
{
    private const SENSITIVE_WORDS = [
        "diantre",
        "faquin",
        "tartuffe",
        "coquin"
    ];

    public function purify(string $sentence): string {
        return str_ireplace(self::SENSITIVE_WORDS, "****", $sentence);
    }
}
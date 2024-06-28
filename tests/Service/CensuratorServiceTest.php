<?php

namespace App\Tests\Service;

use App\Service\CensuratorService;
use PHPUnit\Framework\TestCase;

class CensuratorServiceTest extends TestCase
{
    public function test_purify_withAPoliteSentence(): void
    {
        $service = new CensuratorService();
        $sentence = "Une phrase sans gros mot";
        $resultSentence = $service->purify($sentence);

        $this->assertSame($sentence, $resultSentence);
    }

    public function test_purify_withARudeSentence(): void
    {
        $service = new CensuratorService();
        $sentence = "Diantre! Je suis un coquin.";
        $resultSentence = $service->purify($sentence);

        $expectedSentence = "****! Je suis un ****.";

        $this->assertSame($expectedSentence, $resultSentence);
    }

    public function test_purify_withABlankSentence(): void
    {
        $service = new CensuratorService();
        $sentence = "";
        $resultSentence = $service->purify($sentence);

        $this->assertSame($sentence, $resultSentence);
    }
}

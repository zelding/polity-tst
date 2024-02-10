<?php

namespace App\Tests;

use App\Model\EpMember;
use App\Service\ImportService;
use PHPUnit\Framework\TestCase;

class ImportServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $em = new EpMember();

        $svc = new ImportService();

        $ent = $svc->storeNewEpMember($em);

        $this->eq($em, $ent);
    }
}

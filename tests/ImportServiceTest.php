<?php

namespace App\Tests;

use App\Model\EpMember;
use App\Repository\MemberRepository;
use App\Service\ImportService;
use App\Tests\Model\TestEpMember;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportServiceTest extends TestCase
{
    private static string $MEMBER = "<mep><fullName>Magdalena ADAMOWICZ</fullName><country>Poland</country><politicalGroup>Group of the European People's Party (Christian Democrats)</politicalGroup><id>197490</id><nationalPoliticalGroup>Independent</nationalPoliticalGroup></mep>";

    public function testSomething(): void
    {
        $emtest = new TestEpMember(197490, 'Magdalena ADAMOWICZ', 'Poland', 'Group of the European People\'s Party (Christian Democrats)', 'Independent');

        $svc = new class(null, null) extends ImportService {
            public function __construct(
                private readonly ?MemberRepository       $memberRepository,
                private readonly ?HttpClientInterface    $httpClient,
            ) {}
        };

        $em = $svc->parseRawData(self::$MEMBER);

        $this->compareMembers($emtest, $em);
    }

    private function compareMembers(EpMember $a, EpMember $b): void
    {
        static::assertEquals($a->getId(), $b->getId());
        static::assertEquals($a->getCountry(), $b->getCountry());
        static::assertEquals($a->getNationalPoliticalGroup(), $b->getNationalPoliticalGroup());
        static::assertEquals($a->getPoliticalGroup(), $b->getPoliticalGroup());
        static::assertEquals($a->getFullName(), $b->getFullName());
    }

}

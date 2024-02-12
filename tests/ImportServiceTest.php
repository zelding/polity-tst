<?php

namespace App\Tests;

use App\Model\EpMember;
use App\Repository\MemberRepository;
use App\Service\ImportService;
use App\Tests\Model\TestMemberData;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportServiceTest extends TestCase
{
    /**
     * @dataProvider seedList
     * @param string $seed
     * @return void
     */
    public function testParseRawData(string $seed): void
    {
        $td = $this::generateTestMemberData($seed);

        $svc = new class(null, null) extends ImportService {
            public function __construct(
                private readonly ?MemberRepository       $memberRepository,
                private readonly ?HttpClientInterface    $httpClient,
            ) {}
        };

        $em = $svc->parseRawData($td->getXml());
        $this->compareMembers($td->getModel(), $em);
    }

    public static function seedList(): array
    {
        $r = range("a", "z");

        return array_combine($r, array_map(function ($i) {return [$i];}, $r));
    }

    // bitcoin
    private  static function generateTestMemberData(?string $seed = null): TestMemberData
    {
        $prev   = "";
        $hashes = [hash('sha256', $seed ?? microtime()), '', '', ''];
        array_walk($hashes, function (&$current) use (&$prev) {
            $current = hash('sha256', $prev.$current);
            $prev    = $current;;
        });

        $td = new TestMemberData([
            "id" => mt_rand(1, 19670),
            "fullName" => implode( " ", str_split($hashes[0], strlen($hashes[0]) / 2)),
            "country" => $hashes[1],
            "politicalGroup" => $hashes[2],
            "nationalPoliticalGroup" => $hashes[3]
        ]);

        return $td;
    }

    private function compareMembers(EpMember $a, EpMember $b): void
    {
        static::assertEquals($a->getId(), $b->getId());
        static::assertEquals($a->getCountry(), $b->getCountry());
        static::assertEquals($a->getNationalPoliticalGroup(), $b->getNationalPoliticalGroup());
        static::assertEquals($a->getPoliticalGroup(), $b->getPoliticalGroup());
        static::assertEquals($a->getFullName(), $b->getFullName());
        static::assertEquals($a->getNameParts(), $b->getNameParts());
    }

}

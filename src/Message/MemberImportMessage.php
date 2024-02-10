<?php

namespace App\Message;

use App\Model\EpMember;

class MemberImportMessage
{
    public function __construct(
        protected readonly EpMember $memberData,
        public    readonly bool     $allowUpdate
    )
    {}

    public function getMemberData(): EpMember
    {
        return $this->memberData;
    }
}

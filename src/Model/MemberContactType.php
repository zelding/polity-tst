<?php

namespace App\Model;

enum MemberContactType: string
{
    case Address = "address";
    case Email = "email";
    case Social = "social";
}

<?php

namespace App\Enums;

enum RoleType: string
{
    case OWNER = 'owner';
    case CASHIER = 'cashier';
    case MEMBER = 'member';
}

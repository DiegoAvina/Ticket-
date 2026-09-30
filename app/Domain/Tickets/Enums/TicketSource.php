<?php

namespace App\Domain\Tickets\Enums;

enum TicketSource: string
{
    case Web = 'web';
    case Teams = 'teams';
    case Email = 'email';
    case Api = 'api';
    case Mobile = 'mobile';
}

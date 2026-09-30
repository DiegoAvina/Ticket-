<?php

namespace App\Domain\Tickets\Enums;

enum TicketMessageType: string
{
    case Public = 'public';
    case Internal = 'internal';
    case System = 'system';
}

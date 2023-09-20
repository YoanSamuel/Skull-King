<?php

namespace App\Entity;

enum SkullKingPhase: string
{
    case ANNOUNCE = 'ANNOUNCE';
    case PLAYCARD = 'PLAYCARD';
    case RESOLVEFOLD = 'RESOLVEFOLD';
    case GAMEOVER = 'GAMEOVER';
}

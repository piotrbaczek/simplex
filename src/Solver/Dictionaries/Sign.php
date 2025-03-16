<?php

namespace pbaczek\simplex\Solver\Dictionaries;

enum Sign: string
{
    case LEQ = '<=';
    case GEQ = '>=';
    case EQ = '=';
}

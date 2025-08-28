<?php

namespace Effectra\LaravelStatus\Enums;


enum EmailTypeEnum: int
{
    case INTERNAL = 1;
    case EXTERNAL = 2;
    case UNKNOWN = 3;
}
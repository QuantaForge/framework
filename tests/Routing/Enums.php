<?php

namespace QuantaQuirk\Tests\Routing;

enum CategoryEnum
{
    case People;
    case Fruits;
}

enum CategoryBackedEnum: string
{
    case People = 'people';
    case Fruits = 'fruits';
}

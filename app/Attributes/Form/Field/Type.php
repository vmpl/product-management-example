<?php declare(strict_types=1);

namespace App\Attributes\Form\Field;

enum Type: string
{
    case Text = 'text';
    case Hidden = 'hidden';
    case Number = 'number';
}

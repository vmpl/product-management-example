<?php

namespace App\Models;

use App\Models\Scopes\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Attributes\Grid;
use App\Attributes\Form;

#[Grid\Paginator]
#[Grid\Column('id', 'ID')]
#[Grid\Column('name', 'Name')]
#[Grid\Column('number', 'Number')]
#[Grid\Column('created_at', 'Created')]
#[Grid\Column('updated_at', 'Updated')]
#[Form]
#[Form\Field('id', Form\Field\Type::Hidden)]
#[Form\Field('name')]
#[Form\Field('number', Form\Field\Type::Number)]
class Product extends Model
{
    use HasFactory;

    protected $table = 'product_base';

    protected static function booted()
    {
        static::addGlobalScope(new Teams);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}

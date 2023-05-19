<?php

namespace App\Models;

use App\Models\Scopes\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Attributes\Grid;
use App\Attributes\Form;

#[Grid\Paginator]
#[Grid\Column('id', 'ID')]
#[Grid\Column('name', 'Name')]
#[Grid\Column('created_at', 'Created')]
#[Grid\Column('updated_at', 'Updated')]
#[Form\Field('name', 'Name')]
class Pack extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $table = 'product_pack';

    protected static function booted()
    {
        static::addGlobalScope(new Teams);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(PackProduct::class, Product::class);
    }
}

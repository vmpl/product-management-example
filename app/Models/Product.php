<?php

namespace App\Models;

use App\Models\Scopes\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Attributes\Grid;
use App\Attributes\Form;

#[Grid\Paginator]
class Product extends Model
{
    use HasFactory;

    #[Grid\Column('ID')]
    protected int $id;

    #[Grid\Column('Name')]
    #[Form\Field('Name')]
    protected string $name;

    #[Grid\Column('Number')]
    #[Form\Field('Number')]
    protected ?int $number;

    #[Grid\Column('Created')]
    protected string $created_at;

    #[Grid\Column('Updated')]
    protected string $updated_at;

    protected $table = 'product_base';

    protected static function booted()
    {
        static::addGlobalScope(new Teams);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected $fillable = ['name', 'number'];
}

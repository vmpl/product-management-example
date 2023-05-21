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
class Pack extends Model
{
    use HasFactory;

    #[Grid\Column('ID')]
    protected int $id;

    #[Grid\Column('Name')]
    #[Form\Field('Name')]
    protected string $name;

    #[Grid\Column('Created')]
    protected string $created_at;

    #[Grid\Column('Updated')]
    protected string $updated_at;

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
    #[Form\Field('Products', component: Form\Field\Component::Children)]
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, PackProduct::class);
    }
}

<?php

namespace App\Models;

use App\Models\Scopes\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Attributes\Grid;
use App\Attributes\Form;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Grid\Paginator]
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    #[Grid\Column('ID')]
    public int $id;

    #[Grid\Column('Name')]
    #[Form\Field('Name', validationRules: 'required')]
    public string $name;

    #[Grid\Column('Number')]
    #[Form\Field('Number', validationRules: 'integer')]
    public ?int $number;

    #[Grid\Column('Created')]
    public string $created_at;

    #[Grid\Column('Updated')]
    public string $updated_at;

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

    public function save(array $options = [])
    {
        $this->setAttribute('updated_at', date('Y-m-d H:i:s'));

        return parent::save($options);
    }
}

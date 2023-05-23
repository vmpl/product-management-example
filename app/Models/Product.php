<?php

namespace App\Models;

use App\Models\Scopes\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Attributes\Grid;
use App\Attributes\Form;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Grid\Paginator]
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    #[Grid\Column('ID', sortNumber: 10)]
    public int $id;

    #[Grid\Column('Name', sortNumber: 30)]
    #[Form\Field('Name', validationRules: 'required')]
    public string $name;

    #[Grid\Column('Number')]
    #[Form\Field('Number', validationRules: 'nullable|integer')]
    public ?int $number;

    #[Grid\Column('Created')]
    public string $created_at;

    #[Grid\Column('Updated')]
    public string $updated_at;

    protected $table = 'product_base';

    protected $fillable = ['name', 'number'];

    protected $with = ['image'];

    protected static function booted()
    {
        static::addGlobalScope(new Teams);
        static::saving(function (self $product) {
            if ($product->isDirty('number')) {
                $numberOriginal = $product->getOriginal('number');
                $number = $product->getAttribute('number');

                if ($numberOriginal !== null) {
                    ProductImage::find($numberOriginal)->delete();
                }

                if ($number !== null) {
                    ProductImage::fetchImageIfNotAvailable($number);
                }
            }
        });
        static::deleted(function (self $product) {
            $number = $product->getAttribute('number');
            if ($number !== null) {
                ProductImage::find($number)->delete();
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    #[Grid\Column('Image', false, false, 20, Grid\Column\Component::Image)]
    public function image(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'id', 'number');
    }

    public function save(array $options = [])
    {
        $this->setAttribute('updated_at', date('Y-m-d H:i:s'));

        return parent::save($options);
    }
}

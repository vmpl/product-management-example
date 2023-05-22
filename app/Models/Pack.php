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

    protected $fillable = ['name', 'products'];

    protected $table = 'product_pack';
    private \Illuminate\Database\Eloquent\Collection $_products;

    protected static function booted()
    {
        $saveRelation = function (self $pack) {
            $productIds = [];
            /** @var Product $product */
            foreach ($pack->_products as $product) {
                $productIds[] = $product->getAttribute('id');
                $data = [
                    'parent_id' => $pack->getAttribute('id'),
                    'child_id' => $product->getAttribute('id'),
                ];
                PackProduct::updateOrInsert($data, $data);
            }

            PackProduct::where('parent_id', $pack->getAttribute('id'))
                ->whereNotIn('child_id', $productIds)
                ->delete();
        };

        static::addGlobalScope(new Teams);
        static::created($saveRelation);
        static::updated($saveRelation);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    #[Form\Field('Products', component: Form\Field\Component::Children)]
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            PackProduct::class,
            'parent_id',
            'id',
            'id',
            'child_id'
        );
    }

    public function setAttribute($key, $value)
    {
        if ($key === 'products') {
            $this->_products = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    public function save(array $options = [])
    {
        $this->setAttribute('updated_at', date('Y-m-d H:i:s'));
        return parent::save($options);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['products'] = $this->products()->get()->toArray();
        return $array;
    }
}

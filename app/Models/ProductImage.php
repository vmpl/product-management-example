<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ProductImage extends Model
{
    use HasFactory;

    protected $table= 'product_image';

    protected $fillable = ['id', 'download_url'];

    public $timestamps = false;

    public static function fetchImageIfNotAvailable(int $id): ?self
    {
        return static::findOr($id, function () use ($id) {
            $response = Http::get("https://picsum.photos/id/{$id}/info");
            if ($response->failed()) {
                return null;
            }

            $downloadUrl = $response->json('download_url');
            return static::create(['id' => $id, 'download_url' => $downloadUrl]);
        });
    }
}

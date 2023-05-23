<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:product-photos:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import new image entities based on column number in the product_base table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productImageIds = Db::table('product_image')
            ->select('id');

        $productNumbers = Db::table('product_base')
            ->whereNotNull('number')
            ->whereNotIn('number', $productImageIds)
            ->select('number');

        $productNumbers->orderBy('number')->each(function ($product) {
            $number = $product->number;
            ProductImage::fetchImageIfNotAvailable($number);
        });

        $productNumbers = ProductImage::whereNotIn(
            'id',
            DB::table('product_base')
                ->select('number')
                ->whereNotNull('number')
        )->select('id');
        ProductImage::whereIn('id', $productNumbers)->delete();
    }
}

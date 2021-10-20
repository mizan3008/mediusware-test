<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function lists(array $request)
    {
        $query = Product::with([
            'variants',
            'variants.variantOne',
            'variants.variantTwo',
            'variants.variantThree',
        ]);

        if (!is_null($request['title'])) {
            $query->where('products.title', 'LIKE', "%{$request['title']}%");
        }

        if (!is_null($request['price_from']) && !is_null($request['price_to'])) {
            $query->whereHas('variants', function (Builder $q) use ($request) {
                $q->whereBetween('product_variant_prices.price', [$request['price_from'], $request['price_to']]);
            });
        }

        if (!is_null($request['date'])) {
            $query->whereRaw('DATE(products.created_at) = ?', [Carbon::parse($request['date'])->format('Y-m-d')]);
        }

        if (!is_null($request['variant'])) {
            $product_id_array = $this->fetchProductIdsByVariant($request['variant']);
            $query->whereIn('products.id', $product_id_array);
        }

        $products = $query->paginate(10)->appends($request);
        $products->pagination_summary = Helper::paginationSummary($products->total(), $products->perPage(), $products->currentPage());

        $products = $this->processProductVariant($products);

        return $products;
    }

    public function create(array $request): bool
    {
        $product = Product::create([
            'title' => $request['title'],
            'sku' => $request['sku'],
            'description' => $request['description'] ?? null
        ]);

        $this->syncProductVariants($product, $request);
        $this->syncProductImages($product, $request);

        return true;
    }

    public function update(Product $product, array $request): bool
    {
        $product->title = $request['title'];
        $product->sku = $request['sku'];
        $product->description = $request['description'] ?? null;
        $product->save();

        $this->syncProductVariants($product, $request);
        $this->syncProductImages($product, $request);

        return true;
    }

    private function syncProductVariants(Product $product, array $request): bool
    {
        if (isset($request['variants']) && count($request['variants']) > 0) {

            ProductVariant::whereProductId($product->id)->delete();

            $tags = $request['variants']['tag'];

            foreach ($tags as $key => $value) {
                $tag_array = explode(',', $value);

                foreach ($tag_array as $my_tag) {
                    ProductVariant::create([
                        'variant' => strtolower($my_tag),
                        'variant_id' => $request['variants']['variant'][$key],
                        'product_id' => $product->id,
                    ]);
                }
            }
        }

        if (isset($request['product_variant_prices']) && count($request['product_variant_prices']) > 0) {

            $productVariants = ProductVariant::whereProductId($product->id)->get();

            ProductVariantPrice::whereProductId($product->id)->delete();

            foreach ($request['product_variant_prices']['variant'] as $key => $value) {

                $product_variant_one = $product_variant_two = $product_variant_three = null;

                $variant_array = explode('/', $value);

                foreach ($variant_array as $tag) {
                    $productVariant = $productVariants->where('variant', strtolower($tag))->first();

                    if (is_null($productVariant)) {
                        continue;
                    }

                    if ($productVariant->variant_id === 1) {
                        $product_variant_one = $productVariant->id;
                    }

                    if ($productVariant->variant_id === 2) {
                        $product_variant_two = $productVariant->id;
                    }

                    if ($productVariant->variant_id === 6) {
                        $product_variant_three = $productVariant->id;
                    }
                }

                ProductVariantPrice::create([
                    'product_variant_one' => $product_variant_one,
                    'product_variant_two' => $product_variant_two,
                    'product_variant_three' => $product_variant_three,
                    'price' => $request['product_variant_prices']['price'][$key],
                    'stock' => $request['product_variant_prices']['stock'][$key],
                    'product_id' => $product->id,
                ]);
            }
        }

        return true;
    }

    private function syncProductImages(Product $product, array $request): bool
    {
        session()->forget('temp_product_images');

        if (!isset($request['product_images']) || count($request['product_images']) < 1) {
            return false;
        }

        ProductImage::whereProductId($product->id)->delete();

        foreach ($request['product_images'] as $path) {
            ProductImage::create([
                'product_id' => $product->id,
                'file_path' => $path,
            ]);
        }

        return true;
    }


    private function processProductVariant($products)
    {
        foreach ($products as $product) {
            $variants = [];
            foreach ($product->variants as $variant) {
                $my_variant = [];

                if (!empty($variant->variantOne->variant)) {
                    $my_variant[] = $variant->variantOne->variant;
                }

                if (!empty($variant->variantTwo->variant)) {
                    $my_variant[] = $variant->variantTwo->variant;
                }

                if (!empty($variant->variantThree->variant)) {
                    $my_variant[] = $variant->variantThree->variant;
                }

                $variants[] = [
                    'variant' => implode('/', $my_variant),
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                ];
            }

            $product->variants = $variants;
        }

        return $products;
    }

    public function productVariantDropdownList()
    {
        return ProductVariant::select([
            'variant'
        ])
            ->groupBy('variant')
            ->orderBy('variant', 'asc')
            ->pluck('variant', 'variant');
    }

    public function fetchProductIdsByVariant(string $variant)
    {
        $variant_sql = DB::raw("
            SELECT
                pvp.product_id
            FROM
                product_variant_prices AS pvp
            WHERE
                pvp.product_variant_one IN(
                    SELECT
                        id
                    FROM
                        product_variants
                    WHERE
                        variant = ?
                )
                OR pvp.product_variant_two IN(
                    SELECT
                        id
                    FROM
                        product_variants
                    WHERE
                        variant = ?
                )
                OR pvp.product_variant_three IN(
                    SELECT
                        id
                    FROM
                        product_variants
                    WHERE
                        variant = ?
                )
            GROUP BY
                pvp.product_id
        ");

        $response = DB::select($variant_sql, [$variant, $variant, $variant]);

        if (count($response) < 1) {
            return [];
        }

        $product_id_array = [];

        foreach ($response as $row) {
            $product_id_array[] = $row->product_id;
        }

        return $product_id_array;
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\Product\CreateFormRequest as ProductCreateFormRequest;
use App\Http\Requests\Product\EditFormRequest as ProductEditFormRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $request_data = [
            'title' => $request->title ?? null,
            'variant' => $request->variant ?? null,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to ?? null,
            'date' => $request->date ?? null,
        ];

        $products = $this->productService->lists($request_data);

        $product_variants_dropdown_list = $this->productService->productVariantDropdownList();

        return view('products.index', compact([
            'products',
            'product_variants_dropdown_list',
            'request_data'
        ]));
    }

    public function create()
    {
        $variants = Variant::pluck('title', 'id');
        return view('products.create', compact('variants'));
    }

    public function store(ProductCreateFormRequest $request)
    {
        $validated_data = $request->validated();

        $validated_data['product_images'] = session()->get('temp_product_images');

        $this->productService->create($validated_data);

        $message = Helper::message('Product has been successfully created.');
        session()->flash('message', $message);

        return response()->json([
            'status' => 'success',
            'message' => 'Product has been successfully created.',
            'data' => null
        ], 200);
    }

    public function show($product)
    {
        // 
    }

    public function edit(Product $product)
    {
        $product_variants = ProductVariant::select([
            'variant',
            'variant_id'
        ])
            ->whereProductId($product->id)
            ->orderBy('variant_id', 'asc')
            ->get();

        $processed_product_variants = [];

        foreach ($product_variants as $product_variant) {
            $processed_product_variants[$product_variant->variant_id][] = $product_variant->variant;
        }

        $product_variant_prices = ProductVariantPrice::whereProductId($product->id)->get();

        $processed_product_variant_prices = [];

        foreach ($product_variant_prices as $product_variant_price) {
            $my_variant = [];

            if (!empty($product_variant_price->variantOne->variant)) {
                $my_variant[] = $product_variant_price->variantOne->variant;
            }

            if (!empty($product_variant_price->variantTwo->variant)) {
                $my_variant[] = $product_variant_price->variantTwo->variant;
            }

            if (!empty($product_variant_price->variantThree->variant)) {
                $my_variant[] = $product_variant_price->variantThree->variant;
            }

            $processed_product_variant_prices[] = [
                'variant' => implode('/', $my_variant),
                'price' => $product_variant_price->price,
                'stock' => $product_variant_price->stock,
            ];
        }

        $product_images = [];

        if (count($product->images) > 0) {
            foreach ($product->images as $image) {
                $product_images[] = $image->file_path;
            }
        }

        session()->put('temp_product_images', $product_images);

        $variants = Variant::pluck('title', 'id');

        return view('products.edit', compact([
            'product',
            'variants',
            'processed_product_variants',
            'processed_product_variant_prices',
            'product_images'
        ]));
    }

    public function update(ProductEditFormRequest $request, Product $product)
    {
        $validated_data = $request->validated();

        $validated_data['product_images'] = session()->get('temp_product_images');

        $this->productService->update($product, $validated_data);

        $message = Helper::message('Product has been successfully updated.');
        session()->flash('message', $message);

        return response()->json([
            'status' => 'success',
            'message' => 'Product has been successfully updated.',
            'data' => null
        ], 200);
    }

    public function destroy(Product $product)
    {
        //
    }

    public function uploadImage(Request $request)
    {
        $path = $request->file('file')->store('products');

        $temp_product_images = session()->get('temp_product_images');

        $temp_product_images[] = $path;

        session()->put('temp_product_images', $temp_product_images);

        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => null
        ], 200);
    }

    public function fetchImageByProduct(Product $product)
    {
        $product_images = [];

        if (count($product->images) > 0) {
            foreach ($product->images as $image) {
                $product_images[] = [
                    'name' => $image->id,
                    'size' => 512,
                    'path' => asset('storage/' . $image->file_path)
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $product_images
        ], 200);
    }
}

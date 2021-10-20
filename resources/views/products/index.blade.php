@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products ({{ $products->total() }})</h1>
    </div>

    <div class="card">
        @include('products.filter_form')

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="15%" class="text-left">Title</th>
                        <th class="text-left">Description</th>
                        <th width="25%" class="text-left">Variant</th>
                        <th width="7%" class="text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                        @forelse($products as $product)
                            <tr>
                                <td class="text-center">{{ $product->id }}</td>
                                <td class="text-left">
                                    {{ $product->title }}
                                    <br>
                                    Created at: {{ \Carbon\Carbon::parse($product->created_at)->diffForHumans() }}
                                </td>
                                <td class="text-left">{{ $product->description }}</td>
                                <td  width="25%" class="text-left">
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
        
                                        @forelse ($product->variants as $variant)
                                            <dt class="col-sm-3 pb-0">
                                                {{ $variant['variant'] }}
                                            </dt>
                                            <dd class="col-sm-9">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-4 pb-0">Price : {{ number_format($variant['price']) }}</dt>
                                                    <dd class="col-sm-8 pb-0">InStock : {{ $variant['stock'] }}</dd>
                                                </dl>
                                            </dd>
                                        @empty
                                            
                                        @endforelse
                                    </dl>
                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $product) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No product found</td></tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>{{ $products->pagination_summary }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

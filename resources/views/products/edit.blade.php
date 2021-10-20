@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product: {{ $product->title }}</h1>
    </div>
    <section>
        {!! Form::model($product, ['id' => 'formUpdateProduct', 'url' => 'product/'.$product->id, 'method'=>'PUT']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Product Name</label>
                                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Product Name']) !!}
                                <small id="ve-title" class="validation-error text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="">Product SKU</label>
                                {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder' => 'Product SKU']) !!}
                                <small id="ve-sku" class="validation-error text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="">Description</label>
                                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                <small id="ve-description" class="validation-error text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                        </div>
                        <div class="card-body border">
                            <div id="my-dropzone" class="dropzone"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                        </div>
                        <div id="variants_option_area" class="card-body">
                            <p>Option</p>
                            @php
                            $remove_slug = 'main';
                            @endphp
                            @forelse ($processed_product_variants as $variant_id => $processed_product_variant)
                                <div id="varian_row_{{ $remove_slug }}" class="row variants_option_row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::select('variants[variant][]', $variants, $variant_id, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::text('variants[tag][]', implode(',', $processed_product_variant), ['class' => 'form-control variant-option', 'onBlur' => 'javascript:prepareOverview()']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <a href="javascript:removeOption('{{$remove_slug}}');" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>

                                @php
                                $remove_slug = $loop->iteration + 1;
                                @endphp
                            @empty
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Option</label>
                                            {!! Form::select('variants[]', $variants, null, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="float-right text-primary" style="cursor: pointer;">Remove</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="card-footer">
                            <button type="button" onclick="javascript:addOption();" class="btn btn-primary">Add another option</button>
                        </div>

                        <div class="card-header text-uppercase">Preview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Variant</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($processed_product_variant_prices as $processed_product_variant_price)
                                            <tr>
                                                <td>
                                                    {!! Form::hidden('product_variant_prices[variant][]', $processed_product_variant_price['variant']) !!}
                                                    {{ $processed_product_variant_price['variant'] }}
                                                </td>
                                                <td>
                                                    <input name='product_variant_prices[price][]' type="number" value="{{ $processed_product_variant_price['price'] }}" class="form-control">
                                                </td>
                                                <td>
                                                    <input name='product_variant_prices[stock][]' type="number" value="{{ $processed_product_variant_price['stock'] }}" class="form-control">
                                                </td>
                                            </tr>
                                        @empty
                                            
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button id="btnUpdateProduct" type="button" class="btn btn-lg btn-primary">Save</button>
            <a href="{{ url('product') }}" class="btn btn-secondary btn-lg">Cancel</a>
        {!! Form::close() !!}
    </section>
@endsection

@section('script')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js' type='text/javascript'></script>

    <script>

        Dropzone.autoDiscover = false;
        $("div#my-dropzone").dropzone({
            url: "{{ url('product/image') }}",
            init: function() { 
                myDropzone = this;
                $.ajax({
                    url: "{{ url('product/image/'.$product->id) }}",
                    type: 'get',
                    data: {request: 2},
                    dataType: 'json',
                    success: function(response){
                        console.log(response);
                        $.each(response.data, function(key,value) {
                            console.log(value);
                            var mockFile = { name: value.name, size: value.size };

                            myDropzone.emit("addedfile", mockFile);
                            myDropzone.emit("thumbnail", mockFile, value.path);
                            myDropzone.emit("complete", mockFile);
                        });
                    }
                });
            }
        });

        $(document).ready(function(e){
            $("#btnUpdateProduct").on("click", function(e){
                e.preventDefault();
                // var dataAction = $(this).attr('data-action');
                var form = $("#formUpdateProduct");
                var frmData = form.serialize();
                var url = form.attr('action');
                ajaxCall("PUT", url, frmData, function (response) {
                    // 422 is validation error code, let's process validation error
                    if(response.code === 422){
                        var errors = response.json.errors;
                        $.each(errors, function (index, value) {
                            $("#ve-" + index).html(value[0]);
                        });
                        return false;
                    }
                    
                    // I am done, let's have a vacation (cool)
                    if(response.code === 200){
                        location.reload();
                    }
                });
            });
        });
    </script>
    @include('products.comon-js')
@endsection

@section('style')
<link href='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.css' type='text/css' rel='stylesheet'>
@endsection
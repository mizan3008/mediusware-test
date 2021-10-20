{!! Form::open(['url' => 'product', 'method' => 'get', 'class' => 'card-header']) !!}

<div class="form-row justify-content-between">
    <div class="col-md-2">
        {!! Form::text('title', $request_data['title'] ?? null, ['class' => 'form-control', 'placeholder' => 'Product Title']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::select('variant', $product_variants_dropdown_list, $request_data['variant'] ?? null, ['class' => 'form-control select2', 'placeholder' => '-- Select --']) !!}
    </div>

    <div class="col-md-3">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Price Range</span>
            </div>
            {!! Form::number('price_from', $request_data['price_from'] ?? null, ['class' => 'form-control', 'placeholder' => 'From']) !!}
            {!! Form::number('price_to', $request_data['price_to'] ?? null, ['class' => 'form-control', 'placeholder' => 'To']) !!}
        </div>
    </div>
    <div class="col-md-2">
        {!! Form::date('date', $request_data['date'] ?? null, ['class' => 'form-control', 'placeholder' => 'Date']) !!}
    </div>
    <div class="col-md-3">
        <div class="input-group">
            <button type="submit" class="btn btn-primary float-right mr-2"><i class="fa fa-search"></i></button>
            <a href="{{ url('product') }}" class="btn btn-primary float-right">Reset</a>
        </div>
    </div>
</div>

{!! Form::close() !!}
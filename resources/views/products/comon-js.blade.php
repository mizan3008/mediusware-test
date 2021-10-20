<script>
function addOption()
{
    var number_of_options = $(".variants_option_row").length;

    if(number_of_options === 3){
        return false;
    }
    
    var new_number_of_options = number_of_options + 1;

    var html = $("#varian_row_main").html();

    html = html.replace("javascript:removeOption('main')", "javascript:removeOption("+new_number_of_options+")");

    $("#variants_option_area").append('<div id="varian_row_'+new_number_of_options+'" class="row variants_option_row">'+html+'</div>');

    if($(".variants_option_row").length === 3){
        $("#addAnotherOptionButton").addClass('disabled');
    }else{
        $("#addAnotherOptionButton").removeClass('disabled');
    }
}

function removeOption(id)
{
    if(id === 'main'){
        return false;
    }

    $("#varian_row_"+id).remove();

    $("#addAnotherOptionButton").removeClass('disabled');
}

function prepareOverview()
{
    var variant_array = new Array();
    $(".variant-option").each(function(){
        var input = $(this);
        var val = input.val();
        variant_array.push(val);
    });

    var color_variant;
    if(variant_array[0] !== ""){
        color_variant = variant_array[0].split(',');
    }
    var size_variant;
    if(typeof(variant_array[1]) != "undefined" && variant_array[1] !== null){
        size_variant = variant_array[1].split(',');
    }

    var style_variant;
    if(typeof(variant_array[2]) != "undefined" && variant_array[2] !== null){
        style_variant = variant_array[2].split(',');
    }

    $("#product_variant_price tbody").empty();

    $.each(color_variant, function( index, color ) {
        var final_variant = new Array();

        if(color !== ""){
            final_variant.push(color);
        }

        if (Array.isArray(size_variant)){
            final_variant.push(size_variant[index]);
        }

        if (Array.isArray(style_variant)){
            final_variant.push(style_variant[index]);
        }

        var string_variant = final_variant.join('/');

        var html = "<tr><td>"+string_variant+"<input type='hidden' value='"+string_variant+"' name='product_variant_prices[variant][]'></td><td width='30%'><input type='number' class='form-control' name='product_variant_prices[price][]'></td><td width='30%'><input type='number' class='form-control' name='product_variant_prices[stock][]'></td></tr>";

        $("#product_variant_price tbody").append(html);
    });
}
</script>
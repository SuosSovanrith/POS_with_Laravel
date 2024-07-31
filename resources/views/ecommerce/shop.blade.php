@extends('layouts.master2')

@section('title', 'Shop')

@section('sidebar_shop', 'active')

@section('content')

<form action="/filtershop" method="post" class="mb-2">
    @csrf   
    <div class="row">
        <div class="col-md-12 col-lg-3">
            <div class="row">
                <div class="col-4 pt-1">
                    <label for="Filter_Category">Category</label>
                </div>
                <div class="col-8">
                    <div class="select-style-1">
                        <div class="select-position select-sm">
                            <select name="Filter_Category" id="Filter_Category">
                                <option value=" " selected>All</option>
                                @foreach ($category as $item)
                                <option value="{{$item->category_id}}" <?php if(isset($filter_category)){ if($filter_category == $item->category_id) echo("selected");}?>>{{$item->category_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                                
                </div>
            </div>                        
        </div>  
        <div class="col-md-6 col-lg-3">
            <div class="row">
                <div class="col-md-0 col-lg-3"></div>
                <div class="col-md-5 pt-1">
                    <label for="Start_Price">Price from</label>
                </div>
                <div class="col-md-4">
                    <input type="number" min="0" step="0.01" class="form-control" name="Start_Price" value="<?php if(isset($start_price)){ echo($start_price); }else{ echo("0");} ?>">                            
                </div>
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="row">
                <div class="col-md-2 pt-1">
                    <label for="End_Price">To</label>
                </div>
                <div class="col-md-4">
                    <input type="number" min="0" step="0.01" class="form-control" name="End_Price" value="<?php if(isset($end_price)){ echo($end_price); }else{ echo("9999");} ?>">
                </div>
            </div> 
        </div> 
        <div class="col-lg-2 p-1">
            <input type="submit" class="btn btn-primary" id="SearchSubmit" value="Filter">
            <input type="submit" class="btn btn-danger" id="SearchClear" value="Clear" formaction="/ecommerce/shop" formmethod="get">
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-6 col-lg-8"></div>
    <div class="col-md-6 col-lg-4">
        <div class="input-style-2">
            <form action="/searchshop" method="post" id="SearchForm">  
                @csrf
                <input type="text" class="form-control" name="Shop_Search" id="Shop_Search" placeholder="Search Shop...">
                <input type="submit" class="form-control" id="BtnSearchShop" style="display: none;">
            </form>
            <span class="icon"> <i class="lni lni-magnifier"></i> </span>
        </div>
    </div>
</div>

<div class="row">
    @if (!$products->isEmpty())
        @foreach ($products as $product)
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4">
                <div class="card-style-1 mb-30">
                    <div class="card-image">
                        <a href="#" class="BtnViewProduct">
                            <img src="{{asset($product->image)}}" >
                        </a>
                    </div>
                    <div class="card-content">  
                        <h6><a href="#" class="BtnViewProduct">{{$product->product_name}}</a></h6>
                        <p>${{$product->price_out}}</p>
                    </div>
                    <table style="display: none">
                        <tr>
                            <td class="min-width p-3" style="width:69px;">
                                <img src="{{asset($product->image)}}" alt="Image" width="69"/>
                                <p style="display:none;">{{$product->image}}</p>
                            </td>
                            <td class="min-width p-3">
                                <p>{{$product->product_id}}</p>
                            </td>
                            <td class="min-width p-3"  style="width: 150px;">
                                <p>{{$product->product_name}}</p>
                            </td>
                            <td class="min-width p-3">
                                <p>{{$product->quantity}}</p>
                            </td>
                            <td class="min-width p-3">
                                <p>{{$product->price_out}}</p>
                            </td>
                            <td class="min-width p-3" style="display: none;">
                                <p>{{$product->category_name}}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>  
        @endforeach
        {{$products->render()}}
    @else
        {{"No products data..."}}
    @endif

</div>


<!-- Product View Modal-->
<div class="modal fade" id="FormModalProduct" tabindex="-1" aria-labelledby="FormModalProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="FormModalProductLabel">Products</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="row g-0" id="ProductViewCard">
                <!-- Content via JQuery -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
        
    // open product view form
    $(".BtnViewProduct").click(function() {
        $("#FormModalProduct").modal("show");

        var table = $(this).closest('.card-style-1').find('table');

        var Image = table.find('td').eq(0).text().trim();
        var Id = table.find('td').eq(1).text().trim();
        var Product_Name = table.find('td').eq(2).text().trim();
        var Quantity = table.find('td').eq(3).text().trim();
        var Price_Out = table.find('td').eq(4).text().trim();
        var Category_Name = table.find('td').eq(5).text().trim();
        var Add = '';

        if(Quantity < 1){
            Quantity = "<span class='status-btn close-btn text-center'>Out of Stock</span>";
            Add = '<a href="#" class="main-btn dark-btn-outline btn-hover btn-sm" style="pointer-events: none;"><i class="lni lni-cart mr-5"></i><b>Add To Cart</b></a>'
        
        }else{
            Add = '<a href="/addtocart/' + Id + '" class="main-btn success-btn-outline btn-hover btn-sm"><i class="lni lni-cart mr-5"></i><b>Add To Cart</b></a>'
        }

        $("#ProductViewCard").html(
        '<div class="col-md-6 justify-content-center p-3">' +
        '<img src="http://127.0.0.1:8000/'+  Image + '" class="img-fluid rounded-start" alt="Product Image" width="100%">' +
        '</div>' +
        '<div class="col-md-6 p-2 pt-2 ">' +
        '<h2>'  + Product_Name + '</h2> <br/>' +
        '<p>' +
        '<b>Category: </b> ' + Category_Name + ' <br/>' +
        '<b>Price: </b> $' + Price_Out + ' <br/>' +
        '<b>Quantity: </b> ' + Quantity + ' <br/><br/>' +
        '</p>' +
        Add +
        '</div>' 
        );

    });

            
    // for search shop
    $('#Shop_Search').keypress(function (e) {
        if (e.which == 13) {
            $('#BtnSearchShop').click();
        }
    });

</script>
@endsection

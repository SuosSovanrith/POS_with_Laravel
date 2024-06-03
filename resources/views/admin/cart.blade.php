@extends('layouts.master')

@section('title', 'Carts')

@section('sidebar_orders', 'active')
@section('sidebar_cart', 'active')

@section('content')
        <div class="row">
            <!-- Left side -->
            <div class="col-md-6">
                <div class="card-style shadow">
                        <div class="row">
                        <!-- Barcode -->
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="ScanBarcode" id="ScanBarcode" placeholder="Scan Barcode...">
                            </div>
                        <!-- Customer -->
                            <div class="col-md-6">
                                <div class="select-style-2">
                                    <div class="select-position select-sm">
                                        <select name="Customer_Id" id="Customer_Id">
                                            @foreach ($customer as $item)
                                            <option value="{{$item->customer_id}}" >{{$item->customer_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Product List -->
                        <div class="row mb-3">

                            <!-- Alert Message -->
                            @if (session('message'))
                            <div class="alert-box {{session('type')}}-alert">
                                <div class="alert">
                                    <p class="text-medium">
                                    {{session('message')}}
                                    {{session()->forget(['message','type']);}}
                                    </p>
                                </div>
                                </div>        
                            @endif
                            
                            <div class="table-wrapper table-responsive">
                                <table class="table table table-hover table-striped" id="TblMain">
                                    <thead>
                                        <tr>
                                            <th class="p-3">Product</th>
                                            <th class="p-3">Quantity</th>
                                            <th class="p-3">Price</th>
                                        </tr>
                                        <!-- end table row-->
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach ($cart as $item)
                                            <tr>
                                                <td class="min-width p-3">
                                                    <p>{{$item->product_name}}</p>
                                                </td>
                                                <td class="min-width p-3">
                                                    <input type="number" class="form-control Cart_Quantity" min="0" name="Cart_Quantity" id="Cart_Quantity" value="{{$item->cart_quantity}}" style="max-width: 52px; display:inline;">
                                                    <p style="display: none;">{{$item->cart_id}}</p>
                                                    <button class="BtnDeleteCart text-danger" style="width: 16px; background:transparent; border-style:none; display:inline;"><i class="lni lni-trash-can"></i></button>
                                                </td>
                                                <td class="min-width p-3">
                                                    <p>${{number_format($item->price_out * $item->cart_quantity, 2, '.', ',')}}</p>
                                                </td>
                                            </tr>   
                                            <?php $total += $item->price_out * $item->cart_quantity; ?>
                                        @endforeach
                                        <!-- end table row -->
                                    </tbody>
                                </table>
                                <!-- end table -->
                            </div>
                        </div>
                        <!-- End Product List -->

                        <!-- Total / Btn-->
                        <div class="row mb-3">
                            <div class="col">
                                <b>Total: </b>
                            </div>
                            <div class="col">
                                <h4 class="text-danger" align="right">${{number_format($total, 2, '.', ',')}}</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <button class="main-btn danger-btn btn-hover BtnClearCart" style="height: 40px;">Clear</button>
                            </div>
                            <div class="col" align="right">
                                <button href="#0" class="main-btn primary-btn btn-hover" style="height: 40px;">Submit</button>
                            </div>
                        </div>
                        <!-- End Total / Btn-->

                    </div>
                </div>
        <!-- End left side -->

        <!-- Right Side -->
            <div class="col-md-6">
                <div class="card-style shadow">
                    <!-- Search Product -->
                    <div class="input-style-2">
                        <form action="/searchproduct" method="post" id="SearchForm">
                            @csrf
                            <input type="text" class="form-control" name="Product_Search" id="Product_Search" placeholder="Search Product...">
                            <input type="submit" class="form-control" id="SearchSubmit" style="display: none;">
                        </form>
                        <span class="icon"> <i class="lni lni-magnifier"></i> </span>
                    </div>

                    <!-- List Products -->
                    <div class="row" id="Product_List">

                        <!-- Product Item-->
                        @foreach ($products as $product)
                            <div class="col-md-3 col-sm-4" style="height: 170px;">
                                <div class="card-style-2 mb-30" style="height: 170px;">
                                    <div class="card-image">
                                        <a href="/addcartimage/{{$product->product_id}}">
                                            <img src="{{asset($product->image)}}" alt="">
                                        </a>
                                    </div>
                                    <div class="card-content">
                                        <h4 align="center">{{$product->product_name}}</h4>
                                    </div>
                                </div>
                            </div>      
                        @endforeach
                        <!-- End Product Item -->

                    </div>
                    <!-- End Product List -->
                </div>
            </div>
        <!-- End right side -->

        </div>
    <!-- End Content -->

@endsection

@section('script')

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // for add cart
    $('#ScanBarcode').keypress(function (e) {
        if (e.which == 13) {
            var Barcode = $("#ScanBarcode").val();
            $.post('/addcart', {
                    Barcode: Barcode
                }, function(data) {
                    window.location.href = "/admin/cart";
            });
        }
    });
    
    // for update quantity
    $('.Cart_Quantity').focusin(function () {
        var Cart_Quantity = $(this).val();

        $('.Cart_Quantity').focusout(function () {
            $(this).val(Cart_Quantity)
        });

    });

    $('.Cart_Quantity').keypress(function (e) {
        if (e.which == 13) {
            var Cart_Quantity = $(this).val();
            var current_row = $(this).closest('tr');
            var Cart_Id = current_row.find('td').eq(1).text().trim();

            $.post('/updatecartquantity', {
                Cart_Quantity: Cart_Quantity,
                Cart_Id: Cart_Id
            }, function(data) {
                window.location.href = "/admin/cart";
            });
        }
    });
    
    // for delete cart
        $(".BtnDeleteCart").click(function() {
            var current_row = $(this).closest('tr');
            var Cart_Id = current_row.find('td').eq(1).text().trim();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/deletecart', {
                    Cart_Id: Cart_Id
                }, function(data) {
                    window.location.href = "/admin/cart";
                });
            }
        });
        $(".BtnClearCart").click(function() {
            if (confirm("Are you sure you clear cart?")) {
                $.post('/clearcart', {
                }, function(data) {
                    window.location.href = "/admin/cart";
                });
            }
        });
    
    // for search product
    $('#Product_Search').keypress(function (e) {
        if (e.which == 13) {
            $('#SearchSubmit').click();
        }
    });

    // for update Product
    $(function() {

        // auto fill form of Product from edit id
        $("#TblMain").on('click', '.BtnEditProduct', function() {
            $("#FormModal").modal("show");

            var current_row = $(this).closest('tr');
            var Image = current_row.find('td').eq(0).text().trim();
            var Id = current_row.find('td').eq(1).text().trim();
            var Product_Name = current_row.find('td').eq(2).text().trim();
            var Barcode = current_row.find('td').eq(3).text().trim();
            var Quantity = current_row.find('td').eq(4).text().trim();
            var Price_In = current_row.find('td').eq(5).text().trim().slice(1);
            var Price_Out = current_row.find('td').eq(6).text().trim().slice(1);
            var Category_Id = current_row.find('td').eq(8).text().trim();
            var Supplier_Id = current_row.find('td').eq(9).text().trim();

            $('#CurrentImage').val(Image);
            $("#Id").val(Id);
            $("#Product_Name").val(Product_Name);
            $("#Category_Id option[value='" + Category_Id + "']").attr("selected","selected");
            $("#Supplier_Id option[value='" + Supplier_Id + "']").attr("selected","selected");
            $("#Barcode").val(Barcode);
            $("#Quantity").val(Quantity);
            $("#Price_In").val(Price_In);
            $("#Price_Out").val(Price_Out);
        });

    });

    // for delete Product
    $(function() {

        $("#TblMain").on('click', '.BtnDeleteProduct', function() {
            var current_row = $(this).closest('tr');
            var Id = current_row.find('td').eq(1).text();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/deleteproduct', {
                    id: Id
                }, function(data) {
                    window.location.href = "/admin/products";
                });
            }
        });
    });

    // open popup form
    $("#AddPopup").click(function() {
        $("#FormModal").modal("show");
    });
    
    // clear form
    $(".btn-close").click(function() {
        $('#CurrentImage').val("");
        $("#Id").val("");
        $("#Product_Name").val("");
        $("#Category_Id option[value='']").attr("selected","selected");
        $("#Supplier_Id option[value='']").attr("selected","selected");
        $("#Barcode").val("");
        $("#Quantity").val("");
        $("#Price_In").val("");
        $("#Price_Out").val("");
    });
    
    
    // open product view form
    $(".BtnViewProduct").click(function() {
        $("#FormModalProduct").modal("show");

        var current_row = $(this).closest('tr');
        var Image = current_row.find('td').eq(0).text().trim();
        var Id = current_row.find('td').eq(1).text().trim();
        var Product_Name = current_row.find('td').eq(2).text().trim();
        var Barcode = current_row.find('td').eq(3).text().trim();
        var Quantity = current_row.find('td').eq(4).text().trim();
        var Price_In = current_row.find('td').eq(5).text().trim().slice(1);
        var Price_Out = current_row.find('td').eq(6).text().trim().slice(1);
        var Category_Name = current_row.find('td').eq(10).text().trim();
        var Supplier_Name = current_row.find('td').eq(11).text().trim();

        if(Quantity < 1){
            In_Stock = "<span class='status-btn close-btn text-center'>Out of Stock</span>";
        }else{
            In_Stock = " <span class='status-btn success-btn text-center'>In Stock</span>";
        }
        
        $("#ProductViewCard").html(
        '<div class="col-md-6 justify-content-center p-3">' +
        '<img src="http://127.0.0.1:8000/'+  Image + '" class="img-fluid rounded-start" alt="Product Image" width="100%">' +
        '</div>' +
        '<div class="col-md-1">' +
        '</div>' + 
        '<div class="col-md-5 p-2 pt-5 ">' +
        '<h2>' +  Id + '. ' + Product_Name + '</h2> <br/>' +
        '<p>' +
        '<b>Category: </b> ' + Category_Name + ' <br/>' +
        '<b>Supplier: </b> ' + Supplier_Name + ' <br/>' +
        '<b>Barcode: </b> ' + Barcode + ' <br/>' +
        '<b>Quantity: </b> ' + Quantity + ' <br/>' +
        '<b>Price In: </b> $' + Price_In + ' <br/>' +
        '<b>Price Out: </b> $' + Price_Out + ' <br/><br/>' +
        '<b>In Stock: </b> ' + In_Stock +
        '</p>' +
        '</div>'
        );

    });

</script>

@endsection
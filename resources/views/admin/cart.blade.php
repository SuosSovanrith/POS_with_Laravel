@extends('layouts.master')

@section('title', 'Carts')

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
                                <div class="select-style-1">
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
                            
                            <div class="table-wrapper table-responsive border-bottom">
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
                                        <?php $total = 0; $cart_item = 0; ?>
                                        @foreach ($cart as $item)
                                            <tr>
                                                <td class="min-width p-3">
                                                    <img src="{{asset($item->image)}}" width="60" style="display:inline;">
                                                    <p  style="display:inline;">{{$item->product_name}}</p>
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
                                            <?php $total += $item->price_out * $item->cart_quantity; $cart_item += 1?>
                                            @endforeach

                                        <input type="hidden" id="TotalAmount" value="<?=$total?>"/> 
                                        <!-- end table row -->
                                    </tbody>
                                </table>
                                <!-- end table -->
                            </div>
                        </div>
                        <!-- End Product List -->

                        <!-- Discount-->
                        <div class="row mb-3">
                            <!-- SubTotal / Btn-->
                                <div class="d-sm-none col-6"></div>
                                <div class="col pt-1 pb-1">
                                    <b>Subtotal </b>
                                </div>
                                <div class="col pt-1 pb-1">
                                    <h4 class="text-danger" align="right">${{number_format($total, 2, '.', ',')}}</h4>
                                </div>
                            </div>
                            
                            <!-- Discount-->
                        <div class="row mb-3">
                            <div class="col pt-1 pb-1">
                                <b>Discount(%) </b>
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="Discount" id="Discount"  min="0" max="100" step="0.01" value="0"/>
                            </div>
                        <!-- Total / Btn-->
                            <div class="col pt-2 ">
                                <b>Total</b>
                            </div>
                            <div class="col pt-2 " id="Total_Discount">
                                <h4 class="text-danger" align="right">${{number_format($total, 2, '.', ',')}}</h4>
                            </div>
                        </div>
                            
                            <!-- Payment Method-->
                        <div class="row mb-4">
                            <div class="col">
                                <b>Payment </b>
                            </div>
                            <div class="col">
                                <input class="form-check-input border border-secondary" type="radio" value="0" name="Payment" id="PaymentCash" checked>
                                <label class="form-check-label" for="Payment">
                                     Cash</label>
                            </div>
                            <div class="col">
                                <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentAba">
                                <label class="form-check-label" for="Payment">
                                     Aba</label>
                            </div>
                            <div class="col">
                                <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentWing">
                                <label class="form-check-label" for="Payment">
                                     Wing</i></label>
                            </div>
                            <div class="col">
                                <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentAcleda">
                                <label class="form-check-label" for="Payment">
                                     Acleda</i></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <button class="main-btn danger-btn btn-hover BtnClearCart" style="height: 40px;" <?php if($cart_item == 0){ echo('disabled'); } ?> >Clear</button>
                            </div>
                            <div class="col" align="right">
                                <button href="#0" class="main-btn primary-btn btn-hover BtnSubmitOrder" style="height: 40px;" <?php if($cart_item == 0){ echo('disabled'); } ?> >Submit</button>
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
                            <div class="col-md-3 col-lg-2 position-relative">
                                @if ($product->quantity > 0)
                                    <span class="position-absolute top-0 end translate-middle badge rounded-pill bg-danger"  style="z-index:2;">
                                    {{$product->quantity}}
                                    </span>   
                                @endif
                                <div class="card-style-2 mb-30 position-relative px-0">
                                    @if ($product->quantity < 1)
                                        <span class="position-absolute bottom-50 text-center" style="background-color: rgba(255, 255, 255, 0.7);">
                                            <h4 class="text-danger">Out of Stock</h4>
                                        </span>
                                    @endif      
                                    <div class="card-image" >
                                        <a href="/addcartimage/{{$product->product_id}}" <?php if($product->in_stock < 1) echo('style="pointer-events: none"'); ?>>
                                            <img src="{{asset($product->image)}}" alt="">
                                        </a>
                                    </div>
                                    <div class="card-content">
                                        <h5 align="center">{{$product->product_name}}</h5>
                                    </div>
                                </div>
                            </div>      
                        @endforeach
                        <!-- End Product Item -->

                    </div>
                    <!-- End Product List -->
                    {{ $products->render()}}
                </div>
            </div>
        <!-- End right side -->

        </div> 
    <!-- End Content -->

@endsection

@section('script')

<script src="https://unpkg.com/@jarstone/dselect/dist/js/dselect.js"></script>
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

        
    // for calculate discount
    $('#Discount').focusout(function () {
        var discount = $(this).val();
        var total = $("#TotalAmount").val();
        var after = total * (1-(discount/100));
        var data = '<h4 class="text-danger" align="right">$' + parseFloat(after).toFixed(2) + '</h4>';

        $("#Total_Discount").html(data); 

    });
        
    // for add order
    $(".BtnSubmitOrder").click(function() {
        if( $('input[name="Payment"]:checked').val() == 0){
            Swal.fire({
                title: "Enter Recieved Amount",
                html:
                    '<b>Amounts($):</b> <input id="swal-input1" class="swal2-input" min="0" step="0.01" value="' + ($('#TotalAmount').val() * (1-($('#Discount').val()/100))) + '"  />',
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var customer_id = $('#Customer_Id').val();
                    var amount = $('#swal-input1').val();
                    var discount = $('#Discount').val();
                    var payment_method = $('input[name="Payment"]:checked').val();

                    $.post('/addorder', {
                        customer_id: customer_id,
                        amount: amount,
                        discount: discount,
                        payment_method: payment_method

                    }, function(data) {
                        window.location.href = "/admin/order";
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                }
                });
        }else{

            var khqrimage = "";

            if($('input[id="PaymentAba"]').is(':checked') == true){
                var khqrimage = "abaQr.jpg";
            }
            if($('input[id="PaymentWing"]').is(':checked') == true){
                var khqrimage = "wingQr.jpg";
            }
            if($('input[id="PaymentAcleda"]').is(':checked') == true){
                var khqrimage = "acQr.jpg";
            }

            Swal.fire({
                title: "Enter Recieved Amount and KHQR",
                html:
                    '<b>Amounts($):</b> <input type="number" id="swal-input1" class="swal2-input" min="0" step="0.01" value="' + ($('#TotalAmount').val() * (1-($('#Discount').val()/100))) + '"  required/> <br/>' +
                    '<img src="http://127.0.0.1:8000/assets/images/payment/' + khqrimage + '" width="180"> <br/>' +
                    '<b>KHQR:</b> <input type="file" id="swal-input2" class="swal2-file" accept="image/*" required>',
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var customer_id = $('#Customer_Id').val();
                    var amount = $('#swal-input1').val();
                    var discount = $('#Discount').val();
                    var payment_method = $('input[name="Payment"]:checked').val();
                    var khqr = $('#swal-input2').val().split('\\').pop();;

                    $.post('/addorder', {
                        customer_id: customer_id,
                        amount: amount,
                        discount: discount,
                        payment_method: payment_method,
                        khqr: khqr

                    }, function(data) {
                        window.location.href = "/admin/order";
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                }
                });
        }
    });

    // Customer Search Select
    var customer_search = document.querySelector("#Customer_Id");

    dselect(customer_search, {
        search: true,
        maxHeight: '700px'
    });
    
</script>

@endsection
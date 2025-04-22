@extends('layouts.master2')

@section('title', 'Cart')

@section('sidebar_cart', 'active')

@section('content')

    <div class="row justify-content-center">
        <div class="card-style shadow col-md-12 col-lg-10">
        
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

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h3>Cart</h3>
                    </div>
                </div>
                
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
                        <b>Total </b>
                    </div>
                    <div class="col pt-1 pb-1">
                        <h4 class="text-danger" align="right">${{number_format($total, 2, '.', ',')}}</h4>
                    </div>
                </div>
                
            <!-- Discount-->
            {{-- <div class="row mb-3">
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
            </div> --}}
                
                <!-- Payment Method-->
            <div class="row mb-4">
                <div class="col">
                    <b>Payment </b>
                </div>
                <div class="col">
                    <input class="form-check-input border border-secondary" type="radio" value="0" name="Payment" id="PaymentCash" checked>
                    <label class="form-check-label" for="Payment">
                        <i class="fa fa-money text-success"></i></label>
                </div>
                <div class="col">
                    <input class="form-check-input border border-secondary" type="radio" value="3" name="Payment" id="PaymentCreditCard" checked>
                    <label class="form-check-label" for="Payment">
                        <i class="fa fa-credit-card-alt text-secondary"></i> <i class="fa fa-cc-mastercard text-warning"></i> <i class="fa fa-cc-visa text-primary"></i></label>
                </div>
                <div class="col">
                    <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentAba">
                    <label class="form-check-label" for="Payment">
                         <img src="{{asset('assets/images/payment/Aba.jpg')}}" alt="Aba" width="26" height="26"></label>
                </div>
                <div class="col">
                    <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentWing">
                    <label class="form-check-label" for="Payment">
                        <img src="{{asset('assets/images/payment/wing.png')}}" alt="Wing" width="26" height="26"></label>
                </div>
                <div class="col">
                    <input class="form-check-input border border-secondary" type="radio" value="1" name="Payment" id="PaymentAcleda">
                    <label class="form-check-label" for="Payment">
                        <img src="{{asset('assets/images/payment/acbank.png')}}" alt="Acleda" width="26" height="26"> </i></label>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <button class="main-btn danger-btn btn-hover BtnClearCart" style="height: 40px;" <?php if($cart_item == 0){ echo('disabled'); } ?> >Clear</button>
                </div>
                <div class="col" align="middle  ">
                    <a href="/ecommerce/shop" class="main-btn light-btn btn-hover" style="height: 40px;">Continue Shopping</a>
                </div>
                <div class="col" align="right">
                    <button href="#0" class="main-btn primary-btn btn-hover BtnSubmitOrder" style="height: 40px;" <?php if($cart_item == 0){ echo('disabled'); } ?> >Submit</button>
                </div>
            </div>
            <!-- End Total / Btn-->

        </div>
    </div>

    

@endsection

@section('script')
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

            $.post('/ecommerce/updatecartquantity', {
                Cart_Quantity: Cart_Quantity,
                Cart_Id: Cart_Id
            }, function(data) {
                window.location.href = "/ecommerce/cart";
            });
        }
    });

    // for delete cart
    $(".BtnDeleteCart").click(function() {
            var current_row = $(this).closest('tr');
            var Cart_Id = current_row.find('td').eq(1).text().trim();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/ecommerce/deletecart', {
                    Cart_Id: Cart_Id
                }, function(data) {
                    window.location.href = "/ecommerce/cart";
                });
            }
        });
        $(".BtnClearCart").click(function() {
            if (confirm("Are you sure you clear cart?")) {
                $.post('/ecommerce/clearcart', {
                }, function(data) {
                    window.location.href = "/ecommerce/cart";
                });
            }
        });

    // for add order
    $(".BtnSubmitOrder").click(function() {
        if( $('input[name="Payment"]:checked').val() == 0){
            Swal.fire({
                title: "Confirm Order?",
                html:
                    '<input id="swal-input1" class="swal2-input" min="0" step="0.01" value="' + $('#TotalAmount').val() + '" required  style="display:none;"/>',
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var amount = $('#swal-input1').val();
                    var discount = 0;
                    var payment_method = $('input[name="Payment"]:checked').val();

                    $.post('/ecommerce/addorder', {
                        amount: amount,
                        discount: discount,
                        payment_method: payment_method

                    }, function(data) {
                        window.location.href = "/ecommerce/order";
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                }
                });

        }else if($('input[name="Payment"]:checked').val() == 1){

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
                title: "Enter KHQR Receipt",
                html:
                    '<input type="number" id="swal-input1" class="swal2-input" min="0" step="0.01" value="' + $('#TotalAmount').val() + '"  required style="display:none;"/> <br/>' +
                    '<img src="http://127.0.0.1:8000/assets/images/payment/' + khqrimage + '" width="180"> <br/>' +
                    '<b>KHQR:</b> <input type="file" id="swal-input2" class="swal2-file" accept="image/*" required>',
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var amount = $('#swal-input1').val();
                    var discount = 0;
                    var payment_method = $('input[name="Payment"]:checked').val();
                    var khqr = $('#swal-input2').val().split('\\').pop();;

                    $.post('/ecommerce/addorder', {
                        amount: amount,
                        discount: discount,
                        payment_method: payment_method,
                        khqr: khqr

                    }, function(data) {
                        window.location.href = "/ecommerce/order";
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                }
                });
        }else{
            Swal.fire({
                title: "Enter Credit Card Information",
                html:
                    '<input type="number" id="swal-input1" class="swal2-input" min="0" step="0.01" value="' + $('#TotalAmount').val() + '"  required style="display:none;"/> <br/>' +
                    '<b>Credit Card:</b> <input type="number" id="swal-input1" class="swal2-input" placeholder="xxxx-xxxx-xxxx" required/> <br/>' +
                    '<b>Expiration Date:</b> <select class="swal2-input" name="Month" id="Month"><option value="January">January</option><option value="February">February</option><option value="March">March</option><option value="April">April</option><option value="May">May</option><option value="June">June</option><option value="July">July</option><option value="August">August</option><option value="September">September</option><option value="October">October</option><option value="November">November</option><option value="December">December</option></select>' +
                    '<select class="swal2-input" name="Year" id="Year"><option value="2024">2024</option><option value="2025">2025</option><option value="2025">2025</option><option value="2026">2026</option></select> <br/>' +
                    '<b>CCV:</b> <input type="number" id="swal-input1" class="swal2-input" min="1000" max="9999" placeholder="xxxx" required/> <br/>'
                    ,
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var amount = $('#swal-input1').val();
                    var discount = 0;
                    var payment_method = $('input[name="Payment"]:checked').val();

                    $.post('/ecommerce/addorder', {
                        amount: amount,
                        discount: discount,
                        payment_method: payment_method

                    }, function(data) {
                        window.location.href = "/ecommerce/order";
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                if (result.isConfirmed) {
                }
                });
        }
    });

</script>
@endsection

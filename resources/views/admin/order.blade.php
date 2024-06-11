@extends('layouts.master')

@section('title', 'Orders')

@section('sidebar_orders', 'active')
@section('sidebar_order', 'active')

@section('content')
<!-- ========== tables-wrapper start ========== -->
<div class="tables-wrapper shadow-sm">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">

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
                    <div class="col-md-6">
                        <h3>Orders</h3>
                    </div>
                    <div class="col-md-6">
                        <div align="right"><a href="/admin/cart" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Order</b></a></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                        <form action="/searchorder" method="post" id="SearchForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="date" class="form-control border border-dark" name="Start_Date" value="<?php if(isset($start_date)){echo($start_date);}?>">
                                </div>
                                <div class="col-md-5">
                                    <input type="date" class="form-control border border-dark" name="End_Date" value="<?php if(isset($end_date)){echo($end_date);}?>">
                                </div>  
                                <div class="col-md-2">
                                    <input type="submit" class="btn btn-primary" id="SearchSubmit" value="Filter">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table table-sm table-hover table-striped" id="TblMain">
                        <thead>
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Customer</th>
                                <th class="p-3">User</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Recieved</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">To Pay</th>
                                <th class="p-3">Time</th>
                                <th class="p-3">Action</th>
                            </tr>
                            <!-- end table row-->
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td class="min-width p-3">
                                    <p>{{$order->order_id}}</p>
                                </td>
                                <td class="min-width p-3"  style="width: 150px;">
                                    <p>{{$order->customer_name}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$order->name}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>${{number_format($order->total, 2, '.', ',')}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>${{number_format($order->amount, 2, '.', ',')}}</p>
                                </td>
                                <td class="min-width p-3">
                                    @if ($order->amount == 0)
                                        <span class="status-btn close-btn text-center" style="width: 80px;">Unpaid</span>
                                    @elseif ($order->amount < $order->total) 
                                        <span class="status-btn warning-btn text-center" style="width: 80px;">Partial</span>
                                    @elseif ($order->amount > $order->total) 
                                        <span class="status-btn info-btn text-center" style="width: 80px;">Change</span>
                                    @else
                                        <span class="status-btn success-btn text-center" style="width: 80px;">Paid</span>
                                    @endif
                                </td>
                                <td class="min-width p-3">
                                    <p>${{number_format($order->total-$order->amount, 2, '.', ',')}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$order->created_at}}</p>
                                </td>
                                <td class="p-3">
                                    <a href="#" class="BtnEditProduct text-primary" style="width: 20px;"><i class="lni lni-pencil-alt"></i></a>
                                    <a href="#" class="BtnViewProduct text-success" style="width: 20px;"><i class="lni lni-eye"></i></a>
                                    <a href="#" class="BtnDeleteProduct text-danger" style="width: 20px;"><i class="lni lni-trash-can"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <!-- end table row -->
                        </tbody>
                    </table>
                    <!-- end table -->
                    {{$orders->render()}}
                    
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
</div>
<!-- ========== tables-wrapper end ========== -->
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

    // Customer Search Select
    var customer_search = document.querySelector("#Customer_Id");

    dselect(customer_search, {
        search: true,
        maxHeight: '700px'
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
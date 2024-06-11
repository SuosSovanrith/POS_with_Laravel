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
                                    <a href="#" class="BtnPrintReceipt text-primary" style="width: 20px;"><i class="lni lni-printer"></i></a>
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

<!-- Modal -->
<div class="modal fade" id="FormModalReceipt" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="col-12">
                <div class="row">
                        <div class="col-lg-12"  id="PrintReceipt">
                            <div class="card">
                                <div class="card-body">
                                    <div class="invoice-title">
                                        <h4 class="float-end font-size-15"><span class="badge bg-success font-size-12 ms-2">Paid</span></h4>
                                        <div class="mb-4">
                                            <h2 class="mb-1 text-muted">POS Ltd</h2>
                                        </div>
                                        <div class="text-muted">
                                            <p class="mb-1">#1235A Phnom Penh, Cambodia</p>
                                            <p class="mb-1"><i class="lni lni-envelope"></i> pos@email.com</p>
                                            <p><i class="lni lni-phone"></i> 069-69-6969</p>
                                        </div>
                                    </div>
                
                                    <hr class="mt-2 mb-2">
                
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="text-muted">
                                                <div class="mt-4">
                                                    <h5 class="font-size-15 mb-1">Order No:</h5>
                                                    <p>001-234-5678</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                        <div class="col-sm-6">
                                            <div class="text-muted text-sm-end">
                                                <div class="mt-4">
                                                    <h5 class="font-size-15 mb-1">Order Date:</h5>
                                                    <p>12 Oct, 2020</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->
                                    
                                    <div class="py-2">
                                        <h5 class="font-size-15">Order Summary</h5>
                
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle table-nowrap mb-0 table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th class="text-end" style="width: 120px;">Total</th>
                                                    </tr>
                                                </thead><!-- end thead -->
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <h5 class="text-truncate font-size-14 mb-1">Black Strap A012</h5>
                                                            </div>
                                                        </td>
                                                        <td>$ 245.50</td>
                                                        <td  class="text-center">1</td>
                                                        <td class="text-end">$ 245.50</td>
                                                    </tr>
                                                    <!-- end tr -->
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <h5 class="text-truncate font-size-14 mb-1">Stainless Steel S010</h5>
                                                            </div>
                                                        </td>
                                                        <td>$ 245.50</td>
                                                        <td class="text-center">2</td>
                                                        <td class="text-end">$491.00</td>
                                                    </tr>
                                                    <!-- end tr -->
                                                    <tr>
                                                        <th scope="row" colspan="3" class="text-end">Total</th>
                                                        <td class="text-end"><h4 class="m-0 fw-semibold">$739.00</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" colspan="3" class="text-end">Cash</th>
                                                        <td class="text-end"><h4 class="m-0 fw-semibold">$739.00</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" colspan="3" class="text-end">Change</th>
                                                        <td class="text-end"><h4 class="m-0 fw-semibold">$0.00</h4></td>
                                                    </tr>
                                                    <!-- end tr -->
                                                </tbody><!-- end tbody -->
                                            </table><!-- end table -->
                                        </div><!-- end table responsive -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="d-print-none p-3">
                            <div class="float-end">
                                <a href="#" class="btn btn-success" id="Print" onclick="printReceipt()"><i class="lni lni-printer"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</div>

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
    // $('#ScanBarcode').keypress(function (e) {
    //     if (e.which == 13) {
    //         var Barcode = $("#ScanBarcode").val();
    //         $.post('/addcart', {
    //                 Barcode: Barcode
    //             }, function(data) {
    //                 window.location.href = "/admin/cart";
    //         });
    //     }
    // });
    
    // // for update quantity
    // $('.Cart_Quantity').focusin(function () {
    //     var Cart_Quantity = $(this).val();

    //     $('.Cart_Quantity').focusout(function () {
    //         $(this).val(Cart_Quantity)
    //     });

    // });

    // $('.Cart_Quantity').keypress(function (e) {
    //     if (e.which == 13) {
    //         var Cart_Quantity = $(this).val();
    //         var current_row = $(this).closest('tr');
    //         var Cart_Id = current_row.find('td').eq(1).text().trim();

    //         $.post('/updatecartquantity', {
    //             Cart_Quantity: Cart_Quantity,
    //             Cart_Id: Cart_Id
    //         }, function(data) {
    //             window.location.href = "/admin/cart";
    //         });
    //     }
    // });
    
    // // for delete cart
    //     $(".BtnDeleteCart").click(function() {
    //         var current_row = $(this).closest('tr');
    //         var Cart_Id = current_row.find('td').eq(1).text().trim();

    //         if (confirm("Are you sure you want to delete?")) {
    //             $.post('/deletecart', {
    //                 Cart_Id: Cart_Id
    //             }, function(data) {
    //                 window.location.href = "/admin/cart";
    //             });
    //         }
    //     });
    //     $(".BtnClearCart").click(function() {
    //         if (confirm("Are you sure you clear cart?")) {
    //             $.post('/clearcart', {
    //             }, function(data) {
    //                 window.location.href = "/admin/cart";
    //             });
    //         }
    //     });
    
    // // for search product
    // $('#Product_Search').keypress(function (e) {
    //     if (e.which == 13) {
    //         $('#SearchSubmit').click();
    //     }
    // });

    // // Customer Search Select
    // var customer_search = document.querySelector("#Customer_Id");

    // dselect(customer_search, {
    //     search: true,
    //     maxHeight: '700px'
    // });

    // // for update Product
    // $(function() {

    //     // auto fill form of Product from edit id
    //     $("#TblMain").on('click', '.BtnEditProduct', function() {
    //         $("#FormModal").modal("show");

    //         var current_row = $(this).closest('tr');
    //         var Image = current_row.find('td').eq(0).text().trim();
    //         var Id = current_row.find('td').eq(1).text().trim();
    //         var Product_Name = current_row.find('td').eq(2).text().trim();
    //         var Barcode = current_row.find('td').eq(3).text().trim();
    //         var Quantity = current_row.find('td').eq(4).text().trim();
    //         var Price_In = current_row.find('td').eq(5).text().trim().slice(1);
    //         var Price_Out = current_row.find('td').eq(6).text().trim().slice(1);
    //         var Category_Id = current_row.find('td').eq(8).text().trim();
    //         var Supplier_Id = current_row.find('td').eq(9).text().trim();

    //         $('#CurrentImage').val(Image);
    //         $("#Id").val(Id);
    //         $("#Product_Name").val(Product_Name);
    //         $("#Category_Id option[value='" + Category_Id + "']").attr("selected","selected");
    //         $("#Supplier_Id option[value='" + Supplier_Id + "']").attr("selected","selected");
    //         $("#Barcode").val(Barcode);
    //         $("#Quantity").val(Quantity);
    //         $("#Price_In").val(Price_In);
    //         $("#Price_Out").val(Price_Out);
    //     });

    // });

    // // for delete Product
    // $(function() {

    //     $("#TblMain").on('click', '.BtnDeleteProduct', function() {
    //         var current_row = $(this).closest('tr');
    //         var Id = current_row.find('td').eq(1).text();

    //         if (confirm("Are you sure you want to delete?")) {
    //             $.post('/deleteproduct', {
    //                 id: Id
    //             }, function(data) {
    //                 window.location.href = "/admin/products";
    //             });
    //         }
    //     });
    // });

    // // open popup form
    // $("#AddPopup").click(function() {
    //     $("#FormModal").modal("show");
    // });
    
    // // clear form
    // $(".btn-close").click(function() {
    //     $('#CurrentImage').val("");
    //     $("#Id").val("");
    //     $("#Product_Name").val("");
    //     $("#Category_Id option[value='']").attr("selected","selected");
    //     $("#Supplier_Id option[value='']").attr("selected","selected");
    //     $("#Barcode").val("");
    //     $("#Quantity").val("");
    //     $("#Price_In").val("");
    //     $("#Price_Out").val("");
    // });
    
    
    // open print receipt
    $(".BtnPrintReceipt").click(function() {
        $("#FormModalReceipt").modal("show");

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

    // Print
    function printReceipt() { 
        var data = document.getElementById('PrintReceipt').innerHTML;
        var a = window.open('', 'myWin', 'height=800, width=800');
        a.document.write('<html>'); 
        a.document.write('<head> <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"</head>'); 
        a.document.write('<body >'); 
        a.document.write(data); 
        // a.document.write('<div class="d-print-none p-3"><div class="float-end"><a href="#" class="btn btn-success" id="Print" onclick="window.print()">Print</a></div></div>'); 
        a.document.write('</body></html>'); 
        a.document.title = 'Print Receipt'; 
        a.focus(); 
        setTimeout(() => {
            a.stop();     
        }, 1000);
        setTimeout(() => {
            a.print(); 
        }, 1200);
    }

</script>

@endsection
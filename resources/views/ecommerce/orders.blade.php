@extends('layouts.master2')

@section('title', 'Order')

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
                        <div align="right"><a href="/ecommerce/shop" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Order</b></a></div>
                    </div>
                </div>

                <div class="table-wrapper table-responsive mt-3">
                    <table class="table table-sm table-hover table-striped" id="TblMain">
                        <thead>
                            <tr class="bg-secondary">
                                <th class="p-3">ID</th>
                                <th class="p-3">Customer</th>
                                <th class="p-3">User</th>
                                <th class="p-3">Discount</th>
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
                            <?php $total_report = 0;  $total_received = 0;?>
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
                                    <p>{{$order->discount}}%</p>
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
                                <td class="min-width p-3" style="display: none">
                                    <p>{{$order->khqr}}</p>
                                </td>
                                <td class="min-width p-3" style="display: none">
                                    <p>{{$order->address}}</p>
                                </td>
                                <td class="p-3">
                                    <a href="#" class="BtnPrintReceipt text-primary" style="width: 20px;"><i class="lni lni-printer"></i></a>
                                    @if ($order->khqr != "")
                                        <a href="#" class="BtnKHQR text-success" style="width: 20px;"><i class="fa fa-qrcode"></i></a>
                                    @endif
                                    {{-- <a href="#" class="BtnDeleteProduct text-danger" style="width: 20px;"><i class="lni lni-trash-can"></i></a> --}}
                                </td>
                            </tr>

                            <?php $total_report += $order->total; $total_received += $order->amount; ?>

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
                            {{-- Content Via Jquery --}}
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
</div>

{{-- Modal KHQR --}}
<div class="modal fade" id="ModalKHQR" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-12"  id="ImageKHQR">
                        {{--  --}}
                    </div><!-- end col -->
                </div>
            </div>
        </div>
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

    
    // open print receipt
    $(".BtnPrintReceipt").click(function() {
        $("#FormModalReceipt").modal("show");

        var current_row = $(this).closest('tr');
        var order_id = current_row.find('td').eq(0).text().trim();
        var customer = current_row.find('td').eq(1).text().trim();
        var user = current_row.find('td').eq(2).text().trim();
        var discount = current_row.find('td').eq(3).text().trim().slice(0,4);
        var total = current_row.find('td').eq(4).text().trim().slice(1);
        var received = current_row.find('td').eq(5).text().trim().slice(1);
        var status = current_row.find('td').eq(6).text().trim();
        var to_pay = current_row.find('td').eq(7).text().trim();
        var time = current_row.find('td').eq(8).text().trim();
        var address = current_row.find('td').eq(10).text().trim();
        var status_color = "";

        if(status=='Paid'){
            status_color = "success";
        }else if(status=='Unpaid'){
            status_color = "danger";
        }else if(status=='Partial'){
            status_color = "warning";
        }else if(status=='Change'){
            status_color = "info";
        }

        var receiptData = '<div class="card"><div class="card-body"><div class="invoice-title">' +
                            '<h4 class="float-end font-size-15"><span class="badge bg-'+status_color+' font-size-12 ms-2">' + status + '</span></h4>' +
                            '<div class="mb-4">' +
                            '<h2 class="mb-1 text-muted">POS Ltd</h2>' +
                            '</div><div class="text-muted"><p class="mb-1">#1235A Phnom Penh, Cambodia</p><p class="mb-1"><i class="lni lni-envelope"></i> pos@email.com</p><p><i class="lni lni-phone"></i> 069-69-6969</p></div></div>' +
                            '<hr class="mt-2 mb-2"><div class="row"><div class="col-sm-6"><div class="text-muted"><div class="mt-2"><h5 class="font-size-15 mb-1">Order No:</h5><p>' + order_id + '</p></div></div></div>' +
                            '<div class="col-sm-6"><div class="text-muted text-sm-end"><div class="mt-2"><h5 class="font-size-15 mb-1">Order Date:</h5><p>' + time + '</p></div></div></div></div>' +
                            '<div class="row"><div class="col-sm-6"><div class="text-muted"><div class="mt-2"><h5 class="font-size-15 mb-1">Customer:</h5><p>' + customer + '</p></div></div></div>' +
                            '<div class="col-sm-6"><div class="text-muted text-sm-end"><div class="mt-2"><h5 class="font-size-15 mb-1">Served By:</h5><p>' + user + '</p></div></div></div></div>' +
                            '<div class="row"><div class="col-sm-6"><div class="text-muted"><div class="mt-2"><h5 class="font-size-15 mb-1">Address:</h5><p>' + address + '</p></div></div></div>' +
                            '<div class="py-2"><h5 class="font-size-15">Order Summary</h5><div class="table-responsive"><table class="table table-sm align-middle table-nowrap mb-0 table-striped">' +
                            '<thead><tr><th>Item</th><th>Price</th><th>Quantity</th><th class="text-end" style="width: 120px;">Total</th></tr></thead> <tbody>';
        
        $.ajax({
            url: '/getorderitem/'+order_id,
            type: 'GET',
            dataType: 'json', 
            success: function(response) {
                console.log(response);

                $.each(response, function(index, item) {
                    receiptData += '<tr><td><div><h5 class="text-truncate font-size-14 mb-1">' + item.product_name + '</h5></div></td>' +
                        '<td> $' +  parseFloat(item.order_price)+ '</td>' +
                        '<td class="text-center">' + item.order_quantity + '</td>' +
                                    '<td class="text-end">$' + parseFloat(item.order_price * item.order_quantity) + '</td></tr>';
                });

                if(parseFloat(received.slice(1)) > parseFloat(total.slice(1))){
                    var change = parseFloat(received.slice(1)) - parseFloat(total.slice(1));
                }else{
                    var change = 0;
                }

                receiptData += '<tr><th scope="row" colspan="3" class="text-end">Subtotal</th><td class="text-end"><h4 class="m-0 fw-semibold">$' + total / (1 - (discount/100)) + '</h4></td></tr>' +
                                '<tr><th scope="row" colspan="3" class="text-end">Discount</th><td class="text-end"><h4 class="m-0 fw-semibold">' + discount + '%</h4></td></tr>' +
                                '<tr><th scope="row" colspan="3" class="text-end">Total</th><td class="text-end"><h4 class="m-0 fw-semibold">$' + total + '</h4></td></tr>' +
                                '<tr><th scope="row" colspan="3" class="text-end">Cash</th><td class="text-end"><h4 class="m-0 fw-semibold">$' + received + '</h4></td></tr>' +
                                '<tr><th scope="row" colspan="3" class="text-end">Change</th><td class="text-end"><h4 class="m-0 fw-semibold">$' + change + '</h4></td></tr>' +
                                '</tbody></table></div></div><hr class="mt-2 mb-2"><p class="mt-1">**Thank you for purchasing at our store!!**</p></div></div>';

                $("#PrintReceipt").html(receiptData);   
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error here
            }
        });
        

    });

    // Print
    function printReceipt() { 
        var data = document.getElementById('PrintReceipt').innerHTML;
        var a = window.open('', 'myWin', 'height=800, width=800');
        a.document.write('<html>'); 
        a.document.write('<head> <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"</head>'); 
        a.document.write('<body >'); 
        a.document.write(data); 
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

    // Show KHQR
    $(".BtnKHQR").click(function() {
        $("#ModalKHQR").modal("show");
        var current_row = $(this).closest('tr');
        var khqr = current_row.find('td').eq(9).text().trim();

        var data = '<img src="http://127.0.0.1:8000/'+  khqr + '" alt="KHQR Image" width="100%">';
        $("#ImageKHQR").html(data);   

    });

</script>
@endsection

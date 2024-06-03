@extends('layouts.master')

@section('title', 'Carts')

@section('sidebar_products', 'active')
@section('sidebar_cart', 'active')

@section('content')
    <div class="tables-wrapper shadow-sm">
        <div class="row">
            <div class="col-md-6 col-lg-5">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="ScanBarcode" id="ScanBarcode" placeholder="Scan Barcode...">
                    </div>
                    <div class="col">
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
            </div>

            <div class="col-md-6">Hi</div>
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
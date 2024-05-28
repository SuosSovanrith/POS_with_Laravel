@extends('layouts.master')

@section('title', 'Product View')

@section('sidebar_products', 'active')
@section('sidebar_product', 'active')

@section('content')

<!-- <div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8">
        <div class="card-style-5 mb-30">
            <div class="row">
                <div class="col-lg-6 card-image justify-content-center">
                    <img src="{{ asset($products->image) }}" alt="Product Image"/>
                </div>
                <div class="col-lg-6 card-content">
                    <h4>{{$products->product_name}}</h4> <br/>
                    <p>
                        <b>ID: </b> {{$products->id}} <br/>
                        <b>Category: </b> {{$products->category_id}} <br/>
                        <b>Supplier: </b> {{$products->supplier_id}} <br/>
                        <b>Barcode: </b> {{$products->barcode}} <br/>
                        <b>Quantity: </b> {{$products->quantity}} <br/>
                        <b>Price In: </b> ${{$products->price_in}} <br/>
                        <b>Price Out: </b> ${{$products->price_out}} <br/><br/>
                        <b>In Stock: </b>                                     
                        @if ($products->quantity == 0)
                            <span class="status-btn close-btn text-center">Out of Stock</span>
                        @else
                            <span class="status-btn success-btn text-center">In Stock</span>    
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="row justify-content-center align-middle">
    <div class="card mb-3 shadow" style="max-width: 600px;">
      <div class="row g-0">
        <div class="col-md-6 justify-content-center align-middle">
          <img src="{{ asset($products->image) }}" class="img-fluid rounded-start" alt="Product Image">
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-5">
          <div class="card-body">
            <h3 class="card-title">{{$products->product_name}}</h3>
            <p>
                <b>ID: </b> {{$products->id}} <br/>
                <b>Category: </b> {{$products->category_id}} <br/>
                <b>Supplier: </b> {{$products->supplier_id}} <br/>
                <b>Barcode: </b> {{$products->barcode}} <br/>
                <b>Quantity: </b> {{$products->quantity}} <br/>
                <b>Price In: </b> ${{$products->price_in}} <br/>
                <b>Price Out: </b> ${{$products->price_out}} <br/><br/>
                <b>In Stock: </b>                                     
                @if ($products->quantity == 0)
                    <span class="status-btn close-btn text-center">Out of Stock</span>
                @else
                    <span class="status-btn success-btn text-center">In Stock</span>    
                @endif
            </p>
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

    // for update Product
    $(function() {

        // auto fill form of Product from edit id
        $("#TblMain").on('click', '.BtnEditProduct', function() {
            $("#FormModal").modal("show");
            $("#Password").removeAttr('required')

            var current_row = $(this).closest('tr');
            var Image = current_row.find('td').eq(0).text().trim();
            var Id = current_row.find('td').eq(1).text().trim();
            var Product_Name = current_row.find('td').eq(2).text().trim();
            var Category_Id = current_row.find('td').eq(3).text().trim();
            var Supplier_Id = current_row.find('td').eq(4).text().trim();
            var Barcode = current_row.find('td').eq(5).text().trim();
            var Quantity = current_row.find('td').eq(6).text().trim();
            var Price_In = current_row.find('td').eq(7).text().trim().slice(1);
            var Price_Out = current_row.find('td').eq(8).text().trim().slice(1);

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
            $('#CurrentPhoto').val("");
            $("#Id").val("");
            $("#Product_Name").val("");
            $("#Category_Id option[value='']").attr("selected","selected");
            $("#Supplier_Id option[value='']").attr("selected","selected");
            $("#Barcode").val("");
            $("#Quantity").val("");
            $("#Price_In").val("");
            $("#Price_Out").val("");
    });
    
</script>

@endsection
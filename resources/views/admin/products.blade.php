@extends('layouts.master')

@section('title', 'Products')

@section('sidebar_products', 'active')
@section('sidebar_product', 'active')

@section('content')

<!-- Modal -->
<div class="modal fade" id="FormModal" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="FormModalLabel">Products Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addproduct" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <section class="container">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Id" class="form-label">Id</label>
                                <input type="text" class="form-control" name="Id" id="Id">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Product_Name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="Product_Name" id="Product_Name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-md-6">
                                <label for="Category_Id" class="form-label">Category</label>
                                <div class="select-style-2">
                                    <div class="select-position select-sm">
                                        <select name="Category_Id" id="Category_Id">
                                            @foreach ($category as $item)
                                                <option value="{{$item->category_id}}" >{{$item->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class=" col-md-6">
                                <label for="Supplier_Id" class="form-label">Supplier</label>
                                <div class="select-style-2">
                                    <div class="select-position select-sm">
                                        <select name="Supplier_Id" id="Supplier_Id">
                                            <option value="">None</option>
                                        {{--    @foreach ($supplier as $item)
                                                <option value="{{$item->supplier_id}}" >{{$item->supplier_name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" min="0" name="Quantity" id="Quantity" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" min="0" name="Barcode" id="Barcode" placeholder="Eg. 12345" required>
                            </div>
                            <!-- <div class="mb-3 col-md-6">
                                <label for="In_Stock" class="form-label">In Stock</label>
                                <div class="select-style-2">
                                    <div class="select-position select-sm">
                                        <select name="In_Stock" id="In_Stock">
                                            <option value="1">In Stock</option>
                                            <option value="0">Out of Stock</option>
                                        </select>     
                                    </div>
                                </div>                       
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Price_In" class="form-label">Price In</label>
                                <input type="number" class="form-control" step="0.01" min="0" name="Price_In" id="Price_In" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Price_Out" class="form-label">Price Out</label>
                                <input type="number" class="form-control" step="0.01" min="0" name="Price_Out" id="Price_Out" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Image" class="form-label">Image</label>
                                <input type="file" class="form-control" name="Image" id="Image">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="CurrentImage" class="form-label">Current Image</label> <br>
                                <input type="text" class="form-control" name="CurrentImage" id="CurrentImage" value="None" disabled>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="BtnAddProduct" class="btn btn-primary" value="Add" />
                    <input type="submit" id="BtnUpdateProduct" class="btn btn-success" value="Update" formaction="/updateproduct"/>
                </div>
            </form>
        </div>
    </div>
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

                <div class="row">
                    <div class="col-md-6">
                        <h3>Products</h3>
                    </div>
                    <div class="col-md-6">
                        <div align="right"><a href="#" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Product</b></a></div>
                    </div>
                </div>
                <div class="table-wrapper table-responsive">
                    <table class="table table-sm table-hover table-striped" id="TblMain">
                        <thead>
                            <tr>
                                <th class="p-3">Image</th>
                                <th class="p-3">ID</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Barcode</th>
                                <th class="p-3">Quantity</th>
                                <th class="p-3">Price In</th>
                                <th class="p-3">Price Out</th>
                                <th class="p-3">In Stock</th>
                                <th class="p-3">Action</th>
                            </tr>
                            <!-- end table row-->
                        </thead>
                        <tbody>
                            @foreach($products as $product)
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
                                    <p>{{$product->barcode}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$product->quantity}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>${{$product->price_in}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>${{$product->price_out}}</p>
                                </td>
                                <td class="min-width p-3">
                                    @if ($product->in_stock == 0)
                                    <span class="status-btn close-btn text-center" style="width: 100px;">Out of Stock</span>
                                    @else
                                    <span class="status-btn success-btn text-center" style="width: 100px;">In Stock</span>    
                                    @endif
                                </td>
                                <td class="min-width p-3" style="display: none;">
                                    <p>{{$product->category_id}}</p>
                                </td>
                                <td class="min-width p-3" style="display: none;">
                                    <p>{{$product->supplier_id}}</p>
                                </td> 
                                <td class="min-width p-3" style="display: none;">
                                    <p>{{$product->category_name}}</p>
                                </td>
                                <td class="min-width p-3" style="display: none;">
                                    <p>{{$product->supplier_name}}</p>
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
                    {{$products->render()}}
                    
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
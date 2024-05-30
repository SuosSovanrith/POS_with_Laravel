@extends('layouts.master')

@section('title', 'Category')

@section('sidebar_products', 'active')
@section('sidebar_category', 'active')

@section('content')

<!-- Modal -->
<div class="modal fade" id="FormModal" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="FormModalLabel">Category Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addcategory" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <section class="container">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="Category_Id" class="form-label">Category Id</label>
                                <input type="text" class="form-control" name="Category_Id" id="Category_Id">
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="Category_Name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" name="Category_Name" id="Category_Name" required>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="BtnAddCategory" class="btn btn-primary" value="Add" />
                    <input type="submit" id="BtnUpdateCategory" class="btn btn-success" value="Update" formaction="/updatecategory"/>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== tables-wrapper start ========== -->
<div class="tables-wrapper">
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
                        <h3>Category</h3>
                    </div>
                    <div class="col-md-6">
                        <div align="right"><a href="#" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Category</b></a></div>
                    </div>
                </div>
                <div class="table-wrapper table-responsive">
                    <table class="table table-hover table-striped" id="TblMain">
                        <thead>
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Action</th>
                            </tr>
                            <!-- end table row-->
                        </thead>
                        <tbody>
                            @foreach($category as $item)
                            <tr>
                                <td class="min-width p-3">
                                    <p>{{$item->category_id}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$item->category_name}}</p>
                                </td>
                                <td class="p-3">
                                    <a href="#" class="BtnEditCategory btn text-primary"><i class="lni lni-pencil-alt"></i></a>
                                    <a href="#" class="BtnDeleteCategory btn text-danger"><i class="lni lni-trash-can"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <!-- end table row -->
                        </tbody>
                    </table>
                    <!-- end table -->
                    {{$category->render()}}
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

    // for update Category
    $(function() {

        // auto fill form of Category from edit id
        $("#TblMain").on('click', '.BtnEditCategory', function() {
            $("#FormModal").modal("show");

            var current_row = $(this).closest('tr');
            var Category_Id = current_row.find('td').eq(0).text().trim();
            var Category_Name = current_row.find('td').eq(1).text().trim();

            $("#Category_Id").val(Category_Id);
            $("#Category_Name").val(Category_Name);
        });

    });

    // for delete Category
    $(function() {

        $("#TblMain").on('click', '.BtnDeleteCategory', function() {
            var current_row = $(this).closest('tr');
            var Category_Id = current_row.find('td').eq(0).text();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/deletecategory', {
                    category_id: Category_Id
                }, function(data) {
                    window.location.href = "/admin/category";
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
        $("#Category_Id").val("");
        $("#Category_Name").val("");
    });


</script>

@endsection
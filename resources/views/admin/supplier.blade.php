@extends('layouts.master')

@section('title', 'Suppliers')

@section('sidebar_people', 'active')
@section('sidebar_suppliers', 'active')

@section('content')

<!-- Modal -->
<div class="modal fade" id="FormModal" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="FormModalLabel">Supplier Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addsupplier" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <section class="container">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Id" class="form-label">Id</label>
                                <input type="text" class="form-control" name="Id" id="Id">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="Name" id="Name" placeholder="Eg. John Pork..." required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="Email" id="Email" placeholder="name@example.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="Phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="Phone" id="Phone" placeholder="Eg. 012345678...">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Address" class="form-label">Address</label>
                                <input type="text" class="form-control" name="Address" id="Address" placeholder="Eg. Phnom Penh,Cambodia...">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="Photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" name="Photo" id="Photo">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="CurrentPhoto" class="form-label">Current Photo</label> <br>
                                <input type="text" class="form-control" name="CurrentPhoto" id="CurrentPhoto" value="None" disabled>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="BtnAddSupplier" class="btn btn-primary" value="Add" />
                    <input type="submit" id="BtnUpdateSupplier" class="btn btn-success" value="Update" formaction="/updatesupplier"/>
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
                        <h3>Suppliers</h3>
                    </div>
                    <div class="col-md-6">
                        <div align="right"><a href="#" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Spplier</b></a></div>
                    </div>
                </div>
                <div class="table-wrapper table-responsive">
                    <table class="table table-hover table-striped" id="TblMain">
                        <thead>
                            <tr>
                                <th class="p-3">Photo</th>
                                <th class="p-3">ID</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Phone Number</th>
                                <th class="p-3">Address</th>
                                <th class="p-3">Action</th>
                            </tr>
                            <!-- end table row-->
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td class="min-width p-3" style="width:96px;">
                                    <img src="{{ asset($supplier->photo) }}" alt="Image" width="96"/>
                                    <p style="display:none;">{{$supplier->photo}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$supplier->supplier_id}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$supplier->supplier_name}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$supplier->supplier_email}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$supplier->phone_number}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$supplier->address}}</p>
                                </td>
                                <td class="p-3">
                                    <a href="#" class="BtnEditSupplier btn text-primary"><i class="lni lni-pencil-alt"></i></a>
                                    <a href="#" class="BtnDeleteSupplier btn text-danger"><i class="lni lni-trash-can"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <!-- end table row -->
                        </tbody>
                    </table>
                    <!-- end table -->
                    {{$suppliers->render()}}
                    
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

    // for update Supplier
    $(function() {

        // auto fill form of Supplier from edit id
        $("#TblMain").on('click', '.BtnEditSupplier', function() {
            $("#FormModal").modal("show");
            $("#Password").removeAttr('required')

            var current_row = $(this).closest('tr');
            var Photo = current_row.find('td').eq(0).text().trim();
            var Id = current_row.find('td').eq(1).text().trim();
            var Name = current_row.find('td').eq(2).text().trim();
            var Email = current_row.find('td').eq(3).text().trim();
            var Phone = current_row.find('td').eq(4).text().trim();
            var Address = current_row.find('td').eq(5).text().trim();

            $('#CurrentPhoto').val(Photo);
            $("#Id").val(Id);
            $("#Name").val(Name);
            $("#Email").val(Email);
            $("#Phone").val(Phone);
            $("#Address").val(Address);
        });

    });

    // for delete Supplier
    $(function() {

        $("#TblMain").on('click', '.BtnDeleteSupplier', function() {
            var current_row = $(this).closest('tr');
            var Id = current_row.find('td').eq(1).text();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/deletesupplier', {
                    id: Id
                }, function(data) {
                    window.location.href = "/admin/supplier";
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
            $("#Name").val("");
            $("#Email").val("");
            $("#Phone").val("");
            $("#Address").val("");
    });
    
</script>

@endsection
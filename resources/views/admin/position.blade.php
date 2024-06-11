@extends('layouts.master')

@section('title', 'Position')

@section('sidebar_position', 'active')

@section('content')

<!-- Modal -->
<div class="modal fade" id="FormModal" tabindex="-1" aria-labelledby="FormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="FormModalLabel">Position Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/addposition" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <section class="container">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label for="Position_Id" class="form-label">Position Id</label>
                                <input type="text" class="form-control" name="Position_Id" id="Position_Id">
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="Position_Name" class="form-label">Position Name</label>
                                <input type="text" class="form-control" name="Position_Name" id="Position_Name" required>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="BtnAddPosition" class="btn btn-primary" value="Add" />
                    <input type="submit" id="BtnUpdatePosition" class="btn btn-success" value="Update" formaction="/updateposition"/>
                </div>
            </form>
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
                        <h3>Position</h3>
                    </div>
                    <div class="col-md-6">
                        <div align="right"><a href="#" id="AddPopup" class="main-btn primary-btn-outline btn-hover btn-sm"><i class="lni lni-plus mr-5"></i><b>New Position</b></a></div>
                    </div>
                </div>
                <div class="table-wrapper table-responsive">
                    <table class="table table-hover table-striped" id="TblMain">
                        <thead>
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Position</th>
                                <th class="p-3">Action</th>
                            </tr>
                            <!-- end table row-->
                        </thead>
                        <tbody>
                            @foreach($position as $item)
                            <tr>
                                <td class="min-width p-3">
                                    <p>{{$item->position_id}}</p>
                                </td>
                                <td class="min-width p-3">
                                    <p>{{$item->position_name}}</p>
                                </td>
                                <td class="p-3">
                                    <a href="#" class="BtnEditPosition btn text-primary"><i class="lni lni-pencil-alt"></i></a>
                                    <a href="#" class="BtnDeletePosition btn text-danger"><i class="lni lni-trash-can"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <!-- end table row -->
                        </tbody>
                    </table>
                    <!-- end table -->
                    {{$position->render()}}

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

    // for update Position
    $(function() {

        // auto fill form of Position from edit id
        $("#TblMain").on('click', '.BtnEditPosition', function() {
            $("#FormModal").modal("show");

            var current_row = $(this).closest('tr');
            var Position_Id = current_row.find('td').eq(0).text().trim();
            var Position_Name = current_row.find('td').eq(1).text().trim();

            $("#Position_Id").val(Position_Id);
            $("#Position_Name").val(Position_Name);
        });

    });

    // for delete position
    $(function() {

        $("#TblMain").on('click', '.BtnDeletePosition', function() {
            var current_row = $(this).closest('tr');
            var Position_Id = current_row.find('td').eq(0).text();

            if (confirm("Are you sure you want to delete?")) {
                $.post('/deleteposition', {
                    position_id: Position_Id
                }, function(data) {
                    window.location.href = "/admin/position";
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
        $("#Position_Id").val("");
        $("#Position_Name").val("");
    });


</script>

@endsection
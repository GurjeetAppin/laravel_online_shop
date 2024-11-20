@extends('admin.layouts.app')
@section('breadcrumb-item')
    Sub Category
@endsection
@section('list')
    Create
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Sub Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('sub-categories.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" name="subCategoryForm" id="subCategoryForm" method="">
            <div class="card">
                <div class="card-body">								
                
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly>	
                                    <p></p>
                                </div>
                            </div>	
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="showHome">Show on Home</label>
                                    <select name="showHome" id="showHome" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @if($categories->isNotEmpty())
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" >{{$category->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                            </div>								
                        </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{route('sub-categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJs')
<script>
    /* Create Dynamic Slug */
    $('#name').change(function(event){
        event.preventDefault();
        let element = $(this);
        $('button[type=submit]').prop('disabled', true);
        $.ajax({
            url : "{{route('getSlug')}}",
            type : 'get',
            data : {title : element.val()},
            datatype : 'json',
            success : function(response){
                $('button[type=submit]').prop('disabled', false);
                if(response['status'] == true){
                    $('#slug').val(response['slug']);
                }
            },
            error : function(jqXHR, exception){
                console.log("Slug is not found");
            }
        });
    });

    // Submit form Data
    $("#subCategoryForm").submit(function(event){
        event.preventDefault();
        let element = $(this);
        $('button[type=submit]').prop('disabled', true);
        $.ajax({
            url : '{{route("sub-categories.store")}}',
            type : 'post',
            data : element.serializeArray(),
            dataType : 'json',
            headers : {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"').attr('content')
            },
            success : function(response){
                $('button[type=submit]').prop('disabled', false);
                if(response['status'] == true){
                    window.location.href = '{{route("sub-categories.index")}}';
                    $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                    $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                    $("#status").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                    $("#category_id").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                }else{
                    var error = response['errors'];
                    if(error['name']){
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(error['name']);
                    }else{
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(error['name']);
                    }
                    if(error['slug']){
                        $('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(error['slug']);
                    }else{
                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(error['slug']);
                    }
                    if(error['status']){
                        $('#status').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(error['status']);
                    }else{
                        $('#status').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(error['status']);
                    }
                    if(error['category']){
                        $('#category_id').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(error['category']);
                    }else{
                        $('#category_id').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(error['category']);
                    }

                }
            }
        });
    });
</script>
@endsection
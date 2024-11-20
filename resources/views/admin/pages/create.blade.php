@extends('admin.layouts.app')
@section('breadcrumb-item') Page @endsection
@section('list') Create @endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Page</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('pages.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" name="pageForm" id="pageForm" method="post">
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
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly>	
                                <p></p>
                            </div>
                        </div>	
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="content">Content</label>
                                <textarea name="content" id="content" class="summernote" cols="30" rows="10"></textarea>
                            </div>								
                        </div>                                    
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button class="btn btn-primary">Create</button>
                <a href="{{ route('pages.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection
@section('customJs')
<script>
    $('#pageForm').submit(function(event){
        event.preventDefault();
        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url : "{{ route('pages.store') }}",
            type : "post",
            data : $(this).serializeArray(),
            dataType : "json",
            success : function(response){
                $("button[type=submit]").prop('disabled',false);
                if(response['status'] == true){
                    $('#name').removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html();
                    $('#slug').removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html();
                    window.location.href = "{{ route('pages.index') }}"
                }else{
                    var errors = response['errors'];
                    if(errors['name']){
                        $('#name').addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors['name']);
                    }else{
                        $('#name').removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html();
                    }
                    if(errors['slug']){
                        $('#slug').addClass('is-invalid').siblings("p").addClass('invalid-feedback').html(errors['slug']);
                    }else{
                        $('#slug').removeClass('is-invalid').siblings("p").removeClass('invalid-feedback').html();
                    }
                }
            },
            error : function(jqXHR, exception, error){
                console.log('Error = '+ error);
            }
        });
    });

    $("#name").change(function(event){
        event.preventDefault();
        element = $(this);
        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url : "{{ route('getSlug') }}",
            type : 'get',
            data : {title : element.val()},
            dataType : 'json',
            success : function(response){
                if(response['status'] == true){
                    $('#slug').val(response['slug']);
                }
            }
        });
    })
</script>
@endsection
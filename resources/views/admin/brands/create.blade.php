@extends('admin.layouts.app')
@section('breadcrumb-item')
    brands
@endsection
@section('list')
    Create
@endsection
@section('content')
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Brands</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('brands.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="createBrandForm" name="createBrandForm">
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
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                </select>	
                                <p></p>
                            </div>
                        </div>											
                    </div>
                </div>							
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="brands.html" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
@endsection()
@section('customJs')
    <script>
       
        // Submit form Data
        $("#createBrandForm").submit(function(event){
            event.preventDefault();
            let element = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url : '{{route("brands.store")}}',
                type : 'post',
                data : element.serializeArray(),
                dataType : 'json',
                headers : {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"').attr('content')
                },
                success : function(response){
                    $('button[type=submit]').prop('disabled', false);
                    if(response['status'] == true){
                        window.location.href = '{{route("brands.index")}}';
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html();
                       
                    }else{
                        //console.log('data == ',response);
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
                    }
                }
            });
        });

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

    </script>
@endsection()
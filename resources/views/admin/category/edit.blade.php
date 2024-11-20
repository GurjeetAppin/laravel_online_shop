@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('category.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="categoryForm" name="categoryForm">
            <!-- @csrf -->
            <?php
           /*  echo "<pre>";
                print_r($categories); */
            ?>
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{$categories->name}}" placeholder="Name">	
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{$categories->slug}}"placeholder="Slug" readonly>	
                            <p></p>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image">Image</label>
                            <input type="hidden" name="image_id" id="image_id" value="">
                            <div id="image" class="dropzone dz-clickable">
                                <div class="dz-message needsclick">
                                    <br>Drop files here or click to upload</br></br>
                                </div>
                            </div>
                        </div>
                        <div>
                           @if(!empty($categories->image))
                            <img src="{{asset('uploads/category/thumb/'.$categories->image)}}" width="100" height="100" alt="Category Image" srcset="">
                            @endif
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                    <option value="1" {{($categories->status == 1) ? 'selected' : ''}}>Active</option>
                                    <option value="2" {{($categories->status == 2) ? 'selected' : ''}}>Block</option>
                            </select>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="showHome">Show on Home</label>
                            <select name="showHome" id="showHome" class="form-control">
                                <option value="Yes" {{($categories->showHome == 'Yes') ? 'selected' : ''}}>Yes</option>
                                <option value="No" {{($categories->showHome == 'No') ? 'selected' : ''}}>No</option>
                            </select>
                        </div>
                    </div>							
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button class="btn btn-primary" type="submit">Update</button>
            <a href="{{route('category.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJs')
    <script>
        $('#categoryForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
           
             // For disabled after the submit buttton
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url : '{{ route("categories.update",$categories->id) }}',
                type : 'put',
                data : element.serializeArray(),
                dataType : 'json',
                success: function(response){
                    // For disabled after the submit buttton
                    $("button[type=submit]").prop('disabled',false);
                    if(response['status'] == true){
                        console.log("Response ==", response);
                        window.location.href="{{route('category.index')}}";
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                    }else{
                        if(response['notFound'] == true){
                            window.location.href="{{route('category.index')}}";
                        }
                        var errors = response['errors'];
                        if(errors['name']){
                            $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
                        }else{
                            $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['slug']){
                            $('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
                        }else{
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }
                },
                error: function(jqXHR, exception){
                    console.log(jqXHR.status);
                    console.log("Something went wrong");
                }

            });
        });
    $("#name").change(function(event){
        event.preventDefault();
        element = $(this);
         // For disabled after the submit buttton
         $("button[type=submit]").prop('disabled',true);
        $.ajax({
                url : '{{ route("getSlug") }}',
                type : 'get',
                data : {title: element.val()},
                dataType : 'json',
                success: function(response){
                    
                    // For disabled after the submit buttton
                    $("button[type=submit]").prop('disabled',false);        
                    if(response['status'] == true){
                        $('#slug').val(response['slug']);
                    }
                },
                errors : function(jqXHR, exception){
                    console.log("Slug is not found");
                }
        });
    });


    Dropzone.autoDiscover = false; // Render the dropzone
    const dropzone = $('#image').dropzone({
        init : function(){
            this.on('addedfile', function(file){
                if(this.files.length > 1){
                    this.removeFile(this.files[0]);
                } // This is used to select the single file at a time.
            });
        },
        url : "{{route('temp-images.create')}}",
        maxFiles : 1, // Upload single file.
        paramName : 'image', // Create input type name image
        addRemoveLinks : true, // Create remove links in image dropzone section
        acceptedFiles : 'image/jpge,image/png,image/gif',
        headers : {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"').attr('content')
        },success: function(file, response){
            //console.log("Image Id == ",response.image_id);
            $('#image_id').val(response.image_id);
        }
    });
        
    </script>
@endsection

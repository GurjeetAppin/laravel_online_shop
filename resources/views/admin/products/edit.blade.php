@extends('admin.layouts.app')
@section('breadcrumb-item')
    Products
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
                <h1>Create Product</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('products.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
     <form action="" name="productUpdate" id="productUpdate" method="put">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">								
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title">Title</label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="{{ $product->title }}">	
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input type="text" name="slug" id="slug" class="form-control" value="{{ $product->slug }}" placeholder="Slug" readonly>	
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="short_description">Short Description</label>
                                        <textarea name="short_description" id="short_description" cols="2" rows="2" class="short_description" placeholder="Short Description">{{ $product->short_description }}</textarea>
                                    </div>
                                </div>   
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{ $product->description }}</textarea>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="shipping_returns">Shipping and Returns</label>
                                        <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="short_description" placeholder="shipping_returns">{{ $product->shipping_returns }}</textarea>
                                    </div>
                                </div>                                         
                            </div>
                        </div>	                                                                      
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Media</h2>								
                            <div id="image" class="dropzone dz-clickable">
                                <div class="dz-message needsclick">    
                                    <br>Drop files here or click to upload.<br><br>                                            
                                </div>
                            </div>
                        </div>	                                                                      
                    </div>
                    <div class="row" id="product-gallery">
                        @if(!empty($productImages))
                            @foreach($productImages as $image)
                            <div class="col-md-3" id="image-row-{{$image->id}}">
                                <div class="card">
                                    <input type="hidden" name="image_array[]" id="" value="{{$image->id}}">
                                    <img src="{{ asset('uploads/products/small/'.$image->image) }}" class="card-img-top" alt="Gallery image" >
                                    <div class="card-body">
                                    <a href="javascript:void(0)" onclick='deleteImage("{{$image->id}}")' class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Pricing</h2>								
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="price">Price</label>
                                        <input type="text" name="price" id="price" class="form-control" value="{{ $product->price }}" placeholder="Price">
                                        <p class="error"></p>	
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="compare_price">Compare at Price</label>
                                        <input type="text" name="compare_price" id="compare_price" class="form-control" value="{{ $product->compare_price }}" placeholder="Compare Price">
                                        <p class="text-muted mt-3">
                                            To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                        </p>	
                                    </div>
                                </div>                                            
                            </div>
                        </div>	                                                                      
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Inventory</h2>								
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku">SKU (Stock Keeping Unit)</label>
                                        <input type="text" name="sku" id="sku" class="form-control" value="{{ $product->sku }}" placeholder="sku">	
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="barcode">Barcode</label>
                                        <input type="text" name="barcode" id="barcode" class="form-control" value="{{ $product->barcode }}" placeholder="Barcode">	
                                    </div>
                                </div>   
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <!-- Hidden field is used when we uncheck then field is not created inside the validation so this situation is handle with hidden field because when we unchecked then hidden field value is created or if we checked the field the checkbox value is created -->
                                            <input type="hidden" name="track_qty" value="No">
                                            <input class="custom-control-input" type="checkbox" id="track_qty" name="track_qty" value="Yes" {{ ($product->track_qty == 'Yes') ? 'checked' : '' }}>
                                            <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <input type="number" min="0" name="qty" id="qty" class="form-control" value="{{ $product->qty }}" placeholder="Qty">	
                                        <p class="error"></p>
                                    </div>
                                </div>                                         
                            </div>
                        </div>	                                                                      
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">	
                            <h2 class="h4 mb-3">Product status</h2>
                            <div class="mb-3">
                                <select name="status" id="status" class="form-control">
                                    <option <?php if(!empty($product->status) && $product->status == '1') { echo 'selected'; }else { echo ''; }  ?> value="1">Active</option>
                                    <option <?php if(!empty($product->status) && $product->status == '0') { echo 'selected'; }else { echo ''; }  ?> value="0">Block</option>
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="card">
                        <div class="card-body">	
                            <h2 class="h4  mb-3">Product category</h2>
                            <div class="mb-3">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select a Category</option>
                                    @if($categories->isNotEmpty())
                                        @foreach($categories as $category)
                                            <option  <?php if( $product->category_id == $category->id) { echo 'selected'; }else { echo ''; }  ?> value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    @endif                                    
                                </select>
                                <p class="error"></p>
                            </div>
                            <div class="mb-3">
                                <label for="category">Sub category</label>
                                <select name="sub_category" id="sub_category" class="form-control">
                                    @if($subCategory->isNotEmpty())
                                        @foreach($subCategory as $subCate)
                                            <option {{ ($product->sub_category_id == $subCate->id) ? 'selected' : '' }} value="{{ $subCate->id }}">{{$subCate->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="card mb-3">
                        <div class="card-body">	
                            <h2 class="h4 mb-3">Product brand</h2>
                            <div class="mb-3">
                                <select name="brand" id="brand" class="form-control">
                                    @if($brands->isNotEmpty())
                                        @foreach($brands as $brand)
                                            <option {{ ($product->brand == $brand->id) ? 'selected' : ''}} value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="card mb-3">
                        <div class="card-body">	
                            <h2 class="h4 mb-3">Featured product</h2>
                            <div class="mb-3">
                                <select name="is_featured" id="is_featured" class="form-control">
                                    <option <?php if(!empty($product->is_featured) && $product->is_featured == 'No') { echo 'selected'; }else { echo ''; }  ?> value="No">No</option>
                                    <option <?php if(!empty($product->is_featured) && $product->is_featured == 'Yes') { echo 'selected'; }else { echo ''; }  ?> value="Yes">Yes</option>                                                
                                </select>
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>    
                    <div class="card mb-3">
                        <div class="card-body">	
                            <h2 class="h4 mb-3">Related Products</h2>
                            <div class="mb-3">
                               <select multiple name="related_products[]" class="related_products w-100" id="related_products">
                                    @if(!empty($relatedProducts))
                                        @foreach($relatedProducts as $relatedProduct)
                                            <option selected value="{{ $relatedProduct->id }}">{{ $relatedProduct->title }}</option>
                                        @endforeach
                                    @endif
                               </select>
                            </div>
                        </div>
                    </div>                                    
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{route('products.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </div>
    </form>
    
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection
@section('customJs')
<script>
    
    // Slug
    $("#title").on('change', function(){
        let element = $(this);
        $('button[type=submit]').prop('disabled', true);
        $.ajax({
            url : '{{route("getSlug")}}',
            type : 'get',
            data : {title : element.val()},
            dataType : 'json',
            success : function(response){
                //console.log('response ==', response);
                $('button[type=submit]').prop('disabled', false);
                if(response['status'] == true){
                    $('#slug').val(response['slug']);
                }
            }
        });
    });

    // Update Products
    $("#productUpdate").submit(function(event){
        event.preventDefault(); // Stop the actual submittion of form
        let formArray = $(this).serializeArray();
        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url : '{{route("products.update", $product->id)}}',
            type : 'put',
            data : formArray,
            dataType : 'json',
            success : function(response){
                $('button[type="submit"]').prop('disabled', false);
                if(response['status'] == true){
                    $('.error').removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('is-invalid');
                    window.location.href = "{{route('products.index')}}";
                }else{
                    let errors = response['errors'];
                    /* if(errors['title']){
                        $('#title').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['title']);
                    }else{
                        $('#title').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    } */
                   $('.error').removeClass('invalid-feedback').html('');
                   $("input[type='text'], select, input[type='number']").removeClass('is-invalid');
                    $.each(errors, function(key, value){
                        $(`#${key}`).addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(value);
                    });
                }
            },
            error : function(){
                console.log("Something went wrong.");
            }

        });
    });

    $('#category').on('change', function(event){
        event.preventDefault();
        let category_id = $(this).val();
        $.ajax({
            url : '{{route("product-subcategories.index")}}',
            type : 'get',
            data : { category_id: category_id},
            dataType : 'json',
            success : function(response){
                //console.log(response);
                $('#sub_category').find('option').not(':first').remove(); // Not remove the first option.
                $.each(response['subCategories'], function(key, item){
                    $('#sub_category').append(`<option value='${item.id}'>${item.name}</option>`)
                });
            },
            error : function(){
                console.log("Categories not found");
                
            }
        });
    });

    Dropzone.autoDiscover = false; // Render the dropzone
        const dropzone = $('#image').dropzone({
        url : "{{route('products-images.update')}}",                
        maxFiles : 10, // Upload single file.
        paramName : 'image', // Create input type name image
        params:{'product_id' : '{{$product->id}}'},    
        addRemoveLinks : true, // Create remove links in image dropzone section
        acceptedFiles : 'image/jpge,image/png,image/gif',
        headers : {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        },success: function(file, response){
            //$('#image_id').val(response.image_id);
            //console.log(response);
            let html = `<div class="col-md-3" id="image-row-${response.image_id}"><div class="card">
                    <input type="hidden" name="image_array[]" id="" value="${response.image_id}">
                    <img src="${response.imagePath}" class="card-img-top" alt="Gallery image" >
                    <div class="card-body">
                       <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
                    </div>
                </div></div>`;
            $('#product-gallery').append(html);
        },
        complete: function(file){
            this.removeFile(file);
        }
    });

    function deleteImage(id){
       $('#image-row-'+id).remove();
        if(confirm("Are you sure you want to delete image?")){
            $.ajax({
                url: '{{route("products-image-destroy")}}',
                type: 'delete',
                data: {id: id},
                success: function(response){
                    if(response.status == true){
                        alert(response.message);
                    }else{
                        alert(response.message);
                    }
                }
            });
        }
    }

    // Select 2
    $('.related_products').select2({
        ajax: {
            url: '{{ route("products.getProducts") }}',
            dataType: 'json',
            tags: true,
            multiple: true,
            minimumInputLength: 3,
            processResults: function (data) {
                return {
                    results: data.tags
                };
            }
        }
    }); 
</script>
@endsection

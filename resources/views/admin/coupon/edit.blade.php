@extends('admin.layouts.app')
@section('breadcrumb-item') Discount @endsection
@section('list') Update @endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Coupon Code</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('coupons.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="discountForm" name="discountForm">
            <!-- @csrf -->
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code">Code</label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="Coupon Code" value="{{ (!empty($coupon['code'])) ? $coupon['code'] : ''}}">	
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Coupon Code Name" value="{{ (!empty($coupon['name'])) ? $coupon['name'] : ''}}">	
                            <p></p>
                        </div>
                    </div>	
                    	
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="max_uses">Max Uses</label>
                            <input type="number" name="max_uses" id="max_uses" class="form-control" placeholder="Max Uses" value="{{ (!empty($coupon['max_uses'])) ? $coupon['max_uses'] : ''}}">	
                            <p></p>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_uses_user">Max Uses User</label>
                            <input type="number" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="Max Uses User"  value="{{ (!empty($coupon['max_uses_user'])) ? $coupon['max_uses_user'] : ''}}">	
                            <p></p> 
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="percent" {{ ($coupon['type'] == "percent") ? 'selected' : ''}}>Percent</option>
                                <option value="fixed" {{ ($coupon['type'] == "fixed") ? 'selected' : ''}}>Fixed</option>
                            </select>
                            <p></p>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="discount_amount	">Discount Amount</label>
                            <input type="number" name="discount_amount" id="discount_amount" class="form-control" placeholder="Discount Amount" value="{{ (!empty($coupon['discount_amount'])) ? $coupon['discount_amount'] : ''}}">	
                            <p></p> 
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_amount">Min Amount</label>
                            <input type="number" name="min_amount" id="min_amount" class="form-control" placeholder="Min Amount" value="{{ (!empty($coupon['min_amount'])) ? $coupon['min_amount'] : ''}}">	
                            <p></p> 
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">

                                <option value="1" {{ ($coupon['status'] == 1) ? 'selected' : ''}}>Active</option>
                                <option value="2" {{ ($coupon['status'] == 2) ? 'selected' : ''}}>Block</option>
                            </select>
                            <p></p>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="starts_at">Start At</label>
                            <input type="text" name="starts_at" id="starts_at" autocomplete="off" class="form-control" placeholder="Start At" value="{{ (!empty($coupon['starts_at'])) ? $coupon['starts_at'] : ''}}">	
                            <p></p> 
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="expires_at">Expires At</label>
                            <input type="text" name="expires_at" id="expires_at" autocomplete="off"   class="form-control" placeholder="Expire At" value="{{ (!empty($coupon['expires_at'])) ? $coupon['expires_at'] : ''}}">	
                            <p></p> 
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="description">Description</label>
                            <textarea name="description" class="form-control" id="description" cols="30">{{ (!empty($coupon['description'])) ? $coupon['description'] : ''}}</textarea>
                            <p></p>
                        </div>
                    </div>	
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button class="btn btn-primary" type="submit">Update</button>
            <a href="{{route('coupons.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJs')
    <script>
        // Datepicker
        $(document).ready(function(){
            $('#starts_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
            $('#expires_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
        });
        $('#discountForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
             // For disabled after the submit buttton
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url : '{{ route("coupons.update", $coupon->id) }}',
                type : 'put',
                data : element.serializeArray(),
                dataType : 'json',
                success: function(response){
                    // For disabled after the submit buttton
                    $("button[type=submit]").prop('disabled',false);
                    if(response['status'] == true){
                        window.location.href="{{route('coupons.index')}}";
                        $('#code').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#type').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                        $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                        $('#status').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                        $('#starts_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                        $('#expires_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
                    }else{
                        var errors = response['errors'];
                        if(errors['code']){
                            $('#code').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['code']);
                        }else{
                            $('#code').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['type']){
                            $('#type').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['type']);
                        }else{
                            $('#type').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['discount_amount']){
                            $('#discount_amount').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['discount_amount']);
                        }else{
                            $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['status']){
                            $('#status').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['status']);
                        }else{
                            $('#status').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['starts_at']){
                            $('#starts_at').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['starts_at']);
                        }else{
                            $('#starts_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if(errors['expires_at']){
                            $('#expires_at').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['expires_at']);
                        }else{
                            $('#expires_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }
                },
                error: function(jqXHR, exception,errors){
                    console.log("Something went wrong"+errors);
                }

            });
        });
        
    </script>
@endsection

@extends('admin.layouts.app')
@section('breadcrumb-item') Shipping @endsection
@section('list') Create @endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Shipping Management</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('shipping.create')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="put" id="shippingForm" name="shippingForm">
            <!-- @csrf -->
            <div class="card">
                <div class="card-body">	
                @include('admin.message')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <select name="country" id="country" class="form-control">
                                    <option value="">Select a country</option>
                                        @if($countries->isNotEmpty())
                                            @foreach($countries as $country)
                                                <option {{ ($shippingCharger->country_id == $country->id ) ? 'selected' : "" }} value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                                <option {{ ($shippingCharger->country_id ==  'rest_of_world') ? 'selected' : "" }} value="rest_of_world">Rest of the world</option>
                                        @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="amount" id="amount" value="{{ (!empty($shippingCharger->amount)) ? $shippingCharger->amount : ''}}" class="form-control" placeholder="Amount">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </div>
                    </div>
                </div>							
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJs')
    <script>
        $('#shippingForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
             // For disabled after the submit buttton
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url : '{{ route("shipping.update", $shippingCharger->id) }}',
                type : 'put',
                data : element.serializeArray(),
                dataType : 'json',
                success: function(response){
                    // For disabled after the submit buttton
                    $("button[type=submit]").prop('disabled',false);
                    if(response['status'] == true){
                        window.location.href="{{ route('shipping.create') }}";
                        $('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                    }else{
                        var errors = response['errors'];
                        if(errors['amount']){
                            $('#amount').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['amount']);
                        }else{
                            $('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        }

                        if(errors['country']){
                            $('#country').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['country']);
                        }else{
                            $('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        }
                    }
                },
                error: function(jqXHR, exception){
                    console.log("Something went wrong");
                }

            });
        });
   
    </script>
@endsection

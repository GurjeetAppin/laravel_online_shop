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
        <form action="" method="post" id="shippingForm" name="shippingForm">
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
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                        <option value="rest_of_world">Rest of the world</option>
                                    @endif
                            </select>
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount">
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button class="btn btn-primary" type="submit">Create</button>
                        </div>
                    </div>
                </div>
            </div>							
        </div>
        </form>
        <div class="card">
            <div class="card-body">	
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>Id</td>
                                    <td>Country Name</td>
                                    <td>Amount</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @if($shippingCharger->isNotEmpty())
                                    @foreach($shippingCharger as $shipCharger)
                                        <tr>
                                            <td>{{ $shipCharger->id }}</td>
                                            <td>{{ ($shipCharger->country_id == 'rest_of_world' ? 'Rest of the World' : $shipCharger->name) }}</td>
                                            <td>{{ $shipCharger->amount }}</td>
                                            <td>
                                                <a href="{{route('shipping.edit',$shipCharger->id)}}">
                                                    <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                                    </svg>
                                                </a>
                                                <a href="javascript.void(0)" onclick='deleteShipping("{{$shipCharger->id}}")' class="text-danger w-4 h-4 mr-1">
                                                    <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path	ath fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                url : '{{ route("shipping.store") }}',
                type : 'post',
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

        function deleteShipping(id){
            var url = '{{ route("shipping.delete","id") }}';
            var newUrl = url.replace("id", id);
            if(confirm('Are you sure you want to delete shipping charges')){
                $.ajax({
                   url: newUrl,
                   type: 'delete',
                   data:{id:id},
                   dataType: 'json',
                   headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   success: function(response){
                        if(response['status']){
                            window.location.href = "{{ route('shipping.create') }}";
                        }
                   },
                   error: function(jqXHR, exception){

                   }
                });
            }
        }
   
    </script>
@endsection

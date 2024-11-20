@extends('front.layout.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                @include('front.account.common.message')
                <div class="col-md-3">
                   @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form action="" name="profileForm" id="profileForm" method="post">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">               
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" value="{{ (!empty($userData->name)) ? $userData->name : ''}}" placeholder="Enter Your Name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">            
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email"  value="{{ (!empty($userData->email)) ? $userData->email : ''}}" placeholder="Enter Your Email" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">                                    
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" value="{{ (!empty($userData->phone)) ? $userData->phone : ''}}" placeholder="Enter Your Phone" class="form-control">
                                        <p></p>
                                    </div>

                                <!--  <div class="mb-3">                                    
                                        <label for="phone">Address</label>
                                        <textarea name="address" id="address" class="form-control" cols="30" rows="5" placeholder="Enter Your Address"></textarea>
                                    </div> -->

                                    <div class="d-flex">
                                        <button class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card mt-5">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                        </div>
                        <form action="" name="addressForm" id="addressForm" method="post">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">               
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" id="first_name" value="{{ (!empty($customerAddress)) ? $customerAddress->first_name : ''}}" placeholder="Enter Your First name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">               
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" id="last_name" value="{{ (!empty($customerAddress)) ? $customerAddress->last_name : ''}}" placeholder="Enter Your Last name" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">            
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email"  value="{{ (!empty($customerAddress)) ? $customerAddress->email : ''}}" placeholder="Enter Your Email" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="col-md-6 mb-3">                                    
                                        <label for="mobile">Mobile</label>
                                        <input type="text" name="mobile" id="mobile" value="{{ (!empty($customerAddress)) ? $customerAddress->mobile : ''}}" placeholder="Enter Your mobile" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">                                    
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="form-control">
                                            <option value="">Select a Country</option>
                                            @if($countries->isNotEmpty())
                                                @foreach($countries as $country)
                                                    <option 
                                                    {{ (!empty($customerAddress) && $customerAddress->country_id == $country->id) ? 'selected' : ''  }}
                                                    
                                                    value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            @endif                                            
                                        </select>
                                        <p></p>
                                    </div>
                                    <div class="mb-3">                                    
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ (!empty($customerAddress)) ? $customerAddress->address : ''}}</textarea>
                                        <p></p>
                                    </div>
                                    <div class="mb-3 col-md-6">                                    
                                        <label for="apartment">Apartment</label>
                                        <input type="text" name="apartment" value="{{ (!empty($customerAddress)) ? $customerAddress->apartment : ''}}" id="apartment" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                        <p></p>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="city">City</label>
                                        <input type="text" name="city" value="{{ (!empty($customerAddress)) ? $customerAddress->city : ''}}" id="city" class="form-control" placeholder="City">
                                        <p></p>
                                    </div> 
                                    <div class="mb-3 col-md-6">
                                        <label for="state">State</label>
                                        <input type="text" name="state" value="{{ (!empty($customerAddress)) ? $customerAddress->state : ''}}"  id="state" class="form-control" placeholder="State">
                                        <p></p>
                                    </div>  
                                    <div class="mb-3 col-md-6">
                                        <label for="zip">Zip</label>
                                        <input type="text" name="zip" value="{{ (!empty($customerAddress)) ? $customerAddress->zip : ''}}"  id="zip" class="form-control" placeholder="Zip">
                                        <p></p>
                                    </div> 
                                   <div class="d-flex">
                                        <button class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
@endsection
@section('customJs')
<script>
    $('#profileForm').submit(function(event){
        event.preventDefault();
        $.ajax({
            url:'{{ route("account.updateProfile") }}',
            type:'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success : function(response){
                if(response.status == true){
                    window.location.href = '{{ route("account.profile") }}';
                    $('#profileForm #name').siblings("p").removeClass('invalid-feedback').html('');
                    $('#profileForm #name').removeClass('is-invalid'); 

                    $('#profileForm #email').siblings("p").removeClass('invalid-feedback').html('');
                    $('#profileForm #email').removeClass('is-invalid'); 

                    $('#profileForm #phone').siblings("p").removeClass('invalid-feedback').html('');
                    $('#profileForm #phone').removeClass('is-invalid'); 
                }else{
                    var errors= response.errors;
                    if(errors.name){
                    $('#profileForm #name').siblings("p").addClass('invalid-feedback').html(errors.name);
                        $('#profileForm #name').addClass('is-invalid'); 
                    }else{
                        $('#profileForm #name').siblings("p").removeClass('invalid-feedback').html('');
                        $('#profileForm #name').removeClass('is-invalid'); 
                    }
                    if(errors.email){
                    $('#profileForm #email').siblings("p").addClass('invalid-feedback').html(errors.email);
                        $('#profileForm #email').addClass('is-invalid'); 
                    }else{
                        $('#profileForm #email').siblings("p").removeClass('invalid-feedback').html('');
                        $('#profileForm #email').removeClass('is-invalid'); 
                    }
                    if(errors.phone){
                    $('#profileForm #phone').siblings("p").addClass('invalid-feedback').html(errors.phone);
                        $('#profileForm #phone').addClass('is-invalid'); 
                    }else{
                        $('#profileForm #phone').siblings("p").removeClass('invalid-feedback').html('');
                        $('#profileForm #phone').removeClass('is-invalid'); 
                    }
                }
            }

        })
    })

    // addressForm

    $('#addressForm').submit(function(event){
        event.preventDefault();
        $.ajax({
            url:'{{ route("account.updateAddress") }}',
            type:'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success : function(response){
                if(response.status == true){
                    window.location.href = '{{ route("account.profile") }}';
                    $('#first_name').siblings("p").removeClass('invalid-feedback').html('');
                    $('#first_name').removeClass('is-invalid'); 

                    $('#last_name').siblings("p").removeClass('invalid-feedback').html('');
                    $('#last_name').removeClass('is-invalid');

                    $('#addressForm #email').siblings("p").removeClass('invalid-feedback').html('');
                    $('#addressForm #email').removeClass('is-invalid'); 

                    $('#mobile').siblings("p").removeClass('invalid-feedback').html('');
                    $('#mobile').removeClass('is-invalid'); 
                    
                    $('#country').siblings("p").removeClass('invalid-feedback').html('');
                    $('#country').removeClass('is-invalid');

                    $('#address').siblings("p").removeClass('invalid-feedback').html('');
                    $('#address').removeClass('is-invalid');

                    $('#city').siblings("p").removeClass('invalid-feedback').html('');
                    $('#city').removeClass('is-invalid');

                    $('#state').siblings("p").removeClass('invalid-feedback').html('');
                    $('#state').removeClass('is-invalid');

                    $('#zip').siblings("p").removeClass('invalid-feedback').html('');
                    $('#zip').removeClass('is-invalid');
                }else{
                    var errors= response.errors;
                    if(errors.first_name){
                    $('#first_name').siblings("p").addClass('invalid-feedback').html(errors.first_name);
                        $('#first_name').addClass('is-invalid'); 
                    }else{
                        $('#first_name').siblings("p").removeClass('invalid-feedback').html('');
                        $('#first_name').removeClass('is-invalid'); 
                    }
                    if(errors.last_name){
                    $('#last_name').siblings("p").addClass('invalid-feedback').html(errors.last_name);
                        $('#last_name').addClass('is-invalid'); 
                    }else{
                        $('#last_name').siblings("p").removeClass('invalid-feedback').html('');
                        $('#last_name').removeClass('is-invalid'); 
                    }
                    if(errors.email){
                    $('#addressForm #email').siblings("p").addClass('invalid-feedback').html(errors.email);
                        $('#addressForm #email').addClass('is-invalid'); 
                    }else{
                        $('#addressForm #email').siblings("p").removeClass('invalid-feedback').html('');
                        $('#addressForm #email').removeClass('is-invalid'); 
                    }
                    if(errors.mobile){
                    $('#mobile').siblings("p").addClass('invalid-feedback').html(errors.mobile);
                        $('#mobile').addClass('is-invalid'); 
                    }else{
                        $('#mobile').siblings("p").removeClass('invalid-feedback').html('');
                        $('#mobile').removeClass('is-invalid'); 
                    }
                    if(errors.country){
                    $('#country').siblings("p").addClass('invalid-feedback').html(errors.country);
                        $('#country').addClass('is-invalid'); 
                    }else{
                        $('#country').siblings("p").removeClass('invalid-feedback').html('');
                        $('#country').removeClass('is-invalid'); 
                    }
                    if(errors.address){
                    $('#address').siblings("p").addClass('invalid-feedback').html(errors.address);
                        $('#address').addClass('is-invalid'); 
                    }else{
                        $('#address').siblings("p").removeClass('invalid-feedback').html('');
                        $('#address').removeClass('is-invalid'); 
                    }
                    if(errors.apartment){
                    $('#apartment').siblings("p").addClass('invalid-feedback').html(errors.apartment);
                        $('#apartment').addClass('is-invalid'); 
                    }else{
                        $('#apartment').siblings("p").removeClass('invalid-feedback').html('');
                        $('#apartment').removeClass('is-invalid'); 
                    }
                    if(errors.city){
                    $('#city').siblings("p").addClass('invalid-feedback').html(errors.city);
                        $('#city').addClass('is-invalid'); 
                    }else{
                        $('#city').siblings("p").removeClass('invalid-feedback').html('');
                        $('#city').removeClass('is-invalid'); 
                    }
                    if(errors.state){
                    $('#state').siblings("p").addClass('invalid-feedback').html(errors.state);
                        $('#state').addClass('is-invalid'); 
                    }else{
                        $('#state').siblings("p").removeClass('invalid-feedback').html('');
                        $('#state').removeClass('is-invalid'); 
                    }
                    if(errors.zip){
                    $('#zip').siblings("p").addClass('invalid-feedback').html(errors.zip);
                        $('#zip').addClass('is-invalid'); 
                    }else{
                        $('#zip').siblings("p").removeClass('invalid-feedback').html('');
                        $('#zip').removeClass('is-invalid'); 
                    }
                }
            }

        })
    })
</script>
@endsection()
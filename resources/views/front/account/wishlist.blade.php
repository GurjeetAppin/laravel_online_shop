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
                <div class="col-md-12">
                    @include('front.account.common.message')
                </div>
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Orders</h2>
                        </div>
                        <div class="card-body p-4">
                            @if($wishlists->isNotEmpty())
                                @foreach($wishlists as $wishlist)
                                @php
                                    $productImage = getProductImage($wishlist->id);
                                @endphp
                                    <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                        <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                          @if(!empty($productImage))
                                            <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route('front.product', $wishlist->product->slug) }}" style="width: 10rem;"><img class="" src="{{asset('uploads/products/small/'.$productImage->image)}}" alt=""></a>
                                        @else
                                            <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route('front.product', $wishlist->product->slug) }}" style="width: 10rem;"><img class="" src="{{asset('front-assets/images/product-1.jpg')}}" alt="No Image"></a>
                                        @endif
                                            <div class="pt-2">
                                                <h3 class="product-title fs-base mb-2"><a href="{{ route('front.product', $wishlist->product->slug) }}">{{ $wishlist->product->title}}</a></h3>                                        
                                                <div class="fs-lg text-accent pt-2">
                                                <span class="h5"><strong>${{$wishlist->product->price}}</strong></span>
                                                @if($wishlist->product->compare_price > 0)
                                                    <span class="h6 text-underline"><del>${{$wishlist->product->compare_price}}</del></span>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                            <button onclick="removeProduct({{ $wishlist->product_id}})" class="btn btn-outline-danger btn-sm" type="button"><i class="fas fa-trash-alt me-2"></i>Remove</button>
                                        </div>
                                    </div>  
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('customJs')
    <script>
        function removeProduct(id){
            $.ajax({
                url: '{{ route("account.removeProductFromWishlist") }}',
                type: 'post',
                data: {id:id},
                datatype: 'json',
                success: function(response){
                    if(response.status == true){
                        window.location.href = '{{ route("account.wishlist") }}'
                    }
                },
                error: function(error){
                    
                }
            })
        }
    </script>
@endsection

@extends('front.layout.app')
@section('content')
    <section class="container">
        <div class="col-md-12 text-center py-5">
            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                        {{ Session::get('success') }}
                </div>
            @endif
            <h1>Thank you</h1>
            <p>Your Order Id is : {{ $id }}</p>
        </div>
    </section>
@endsection


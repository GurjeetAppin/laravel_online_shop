@extends('admin.layouts.app')
@section('breadcrumb-item')
    Orders
@endsection
@section('list')
    list
@endsection
@section('content')
   <!-- Content Header (Page header) -->
   <section class="content-header">					
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    @include('admin.message')
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                <h1 class="h5 mb-3">Shipping Address</h1>
                                <address>
                                    <strong>{{ $order->first_name.' '.$order->last_name}}</strong><br>
                                    {{ $order->address }}, {{ $order->zip }}, {{ $order->countryName}}<br>
                                    {{ $order->city }}<br>
                                    Phone: {{ $order->mobile }}<br>
                                    Email: {{ $order->email }}
                                </address>
                                <strong>Shipped Date</strong><br>
                                @if(!empty($order->shipped_date))
                                    {{ \Carbon\Carbon::parse($order->shipped_date)->format('d M, Y') }}
                                @else
                                    n/a
                                @endif
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <!-- <b>Invoice #007612</b><br> 
                                    <br>-->
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b> ${{ $order->grand_total }}<br>
                                    <b>Status:</b> <span 
                                    @if($order->status == 'pending')
                                        <span class="text-danger">
                                            Pending
                                        </span>
                                    @elseif($order->status == 'shipped')
                                        <span class="text-info">
                                            Shipped
                                        </span>
                                    @elseif($order->status == 'delivered')
                                        <span class="text-success">
                                            Delivered
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            Cancelled
                                        </span>
                                    @endif
                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">								
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>                                        
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($orderItems->isNotEmpty())
                                        @foreach($orderItems as $orderItem)
                                        <tr>
                                            <td>{{ $orderItem->name}}</td>
                                            <td>${{  number_format($orderItem->price,2) }}</td>
                                            <td>{{ $orderItem->qty }}</td>
                                            <td>${{  number_format($orderItem->total,2) }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td>${{ number_format($order->subtotal,2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Discount: <span class="text-success"> {{ (!empty($order->coupon_code)) ? '('.$order->coupon_code.')' : ''}}</span></th>
                                        <td>${{ number_format($order->discount,2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td>${{ number_format($order->shipping,2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td>${{ number_format($order->grand_total,2) }}</td>
                                    </tr>
                                </tbody>
                            </table>								
                        </div>                            
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <form action="" name="chageOrderStatusForm" id="chageOrderStatusForm" method="post">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ ($order->status == 'pending') ? 'selected' : '' }}>Pending</option>
                                        <option value="shipped" {{ ($order->status == 'shipped') ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ ($order->status == 'delivered') ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ ($order->status == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="shipped_date"></label>
                                    <input type="text" name="shipped_date" placeholder="Shipped Date" class="form-control shipped_date" id="shipped_date" value="{{ $order->shipped_date }}">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                                <h2 class="h4 mb-3">Send Inovice Email</h2>
                                <div class="mb-3">
                                    <select name="userType" id="userType" class="form-control">
                                        <option value="customer">Customer</option>                                                
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Send</button>
                                </div>
                            </form>
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
        $('document').ready(function(){
            $('#shipped_date').datetimepicker({
                Format : 'Y m d H:i:s'
            });
        });

        $('#chageOrderStatusForm').submit(function(event){
            event.preventDefault();
            if(confirm('Are you sure change status?')){
                $.ajax({
                    url : '{{ route("orders.changeOrderStatus", $order->id) }}',
                    type : 'post',
                    data : $(this).serializeArray(),
                    dataType : 'json',
                    success : function(response){
                        window.location.href = '{{ route("orders.detail",$order->id) }}';
                    },
                    error : function(jqXHR, exception, error){

                    }
                });
            }
        });

        $('#sendInvoiceEmail').submit(function(event){
            event.preventDefault();
           if(confirm('Are you sure you want to send email?')){
                $.ajax({
                    url: '{{ route("orders.sendInvoiceEmail", $order->id) }}',
                    type: 'post',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function(response){
                        window.location.href = '{{ route("orders.detail",$order->id) }}';
                    },
                    error: function(jqXHR, exception, error){
                        console.log(error);
                        
                    }
                });
           }
        });
    </script>
@endsection

<!-- User Id Field -->
<div class="col-md-6">

    <!-- Product Name Field -->
    <div class="form-group">
        <h3>{{ $qrcode->product_name }}<br/>
            @if(isset($qrcode->company_name))
                <small>By {{ $qrcode->company_name }}</small>
            @endif    
        </h3>
    </div>

     <!-- Amount Field -->
     <div class="form-group">
        <h4>Amount: Rs {{ $qrcode->amount }}</h4>
    </div>

        <!-- Product Url Field -->
    <div class="form-group">
        {!! Form::label('product_url', 'Product Url:') !!}
        <p> <a href="{{ $qrcode->product_url }}">
                {{ $qrcode->product_url }}
            </a>
        </p>
    </div>

    @if(!Auth::guest() && ($qrcode->user_id == Auth::user()->id || Auth::user()->role_id < 3))
    <hr>
    <!--USER ID/NAME -->
    <div class="form-group">
        {!! Form::label('user_id', 'User Name:') !!}
        <p>{{ $qrcode->user['email'] }}</p>
    </div>

    <!-- Website Field -->
    <div class="form-group">
        {!! Form::label('website', 'Website:') !!}
        <p><a href="{{ $qrcode->website }}" target="_blank">{{ $qrcode->website }}</a></p>
    </div>



    <!-- Callback Url Field -->
    <div class="form-group">
        {!! Form::label('callback_url', 'Callback Url:') !!}
        <p><a href="{{ $qrcode->callback_url }}" target="_blank">{{ $qrcode->callback_url }}</a></p>
    </div>

    <!-- Qrcode Path Field -->
    <div class="form-group">
        {!! Form::label('qrcode_path', 'Qrcode Path:') !!}
        <p>{{ $qrcode->qrcode_path }}</p>
    </div>

    <!-- Status Field -->
    <div class="form-group">
        {!! Form::label('status', 'Status:') !!}
        <p>
            @if($qrcode->status == 1)
                Active
            @else
                Inactive
            @endif           
            
            
            
        </p>
    </div>


@endif
</div>

<div class="col-md-5 pull-right">
     <!-- Qrcode Path field -->
    <div class="form-group">
        {!! Form::label('qrcode_path', 'Scan QRcode for payments:') !!}<br/>
        <img src="{{ asset($qrcode -> qrcode_path) }}">
    </div>
    
        <form method="post" role="form" class="col-md-6" action="{{ route('qrcodes.show_payment_page') }}">
            <div class="form-group">

                <!-- only for logged out users to get see an email field -->
             @if(Auth::guest())
                <label>Enter your email </label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="youremail@gmail.com">
                
                
            </div>

            @else

            <input type="hidden" name="email" value="{{ Auth::user()->email}}">
           
            

            @endif
            
            {{ csrf_field() }} 
            <input type="hidden" name="qrcode_id" value="{{ $qrcode->id }}">   
            <p>

                <button class="btn btn-success btn-lg" type="submit" value="Pay Now!">
                    <i class="fa fa-plus-circle fa-lag"></i> Pay Now!
                </button>
            </p>
        </form>
   


        </div>
</div>

@if(!Auth::guest() && ($qrcode->user_id == Auth::user()->id || Auth::user()->role_id < 3))

<div class="col-xs-12">
<h3 class="text-center"> Transactions done on this QRCode </h3>
@include('transactions.table');

</div>
@endif



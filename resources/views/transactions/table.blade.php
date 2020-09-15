<div class="table-responsive">
    <table class="table" id="transactions-table">
        <thead>
            <tr>
                <th>Qrcode</th>
                <th>Buyer</th>
                <!-- <th>Qrcode Id</th> -->
                <th>Payment Method</th>
                
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <!-- <th colspan="3">Action</th> -->
            </tr>
        </thead>
        <tbody>
        @foreach($transactions  as $transaction)
            <tr>
                
                <td>
                    <a href="{{ route('transactions.show', [$transaction->id]) }}">
                        {{ $transaction->qrcode['product_name'] }}
                    </a>
                    <small>| {{ $transaction->created_at->format('D d, M, Y') }} </small>
                </td>
                <td>{{ $transaction->user['name'] }}</td>
                <td>{{ $transaction->payment_method }}</td>
               
                <td>Rs {{ $transaction->amount }}</td>
                <td>{{ $transaction->status }}</td>
                <td>At {{ $transaction->updated_at->format('D d, M, Y') }}</td>
                
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

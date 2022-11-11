<div class="order-info">
    <link rel="stylesheet" href="{{ asset('css/update-order.css') }}">
    <h2>Update Purchase Order</h2>
    <div class="details">
        <div class="detail">
            <div class="row">
                <span class="material-icons-sharp primary-variant">receipt_long</span>
                <div class="right-side">
                    <h3>
                        <select id="purchased-orders">
                            @unless($purchasedOrders->isEmpty())
                                <option value="default">-- Please Choose one --</option>
                                @foreach ($purchasedOrders as $purchasedOrder)
                                    <option value="{{ $purchasedOrder->id }}" name="{{ $purchasedOrder->payment }}">
                                        PO #{{ $purchasedOrder->id }}</option>
                                @endforeach
                            @endunless
                        </select>
                    </h3>
                    <small class="text-muted">Purchased Order No.</small>
                </div>
            </div>
        </div>
        <div class="detail">
            <div class="row">
                <span class="material-icons-sharp primary-variant">payments</span>
                <div class="right-side">
                    <h3>
                        <select id="order-payment">
                            <option value="default">-- Please Choose one --</option>
                            <option id="paid" value="Paid">Paid</option>
                            <option id="refunded" value="Refunded">Refunded</option>
                        </select>
                    </h3>
                    <small class="text-muted">Payment</small>
                </div>
            </div>
        </div>
        <div class="item update">
            <div class="right">
                <button type="button" id="update-button">
                    <span class="material-icons-sharp">publish</span>
                    <h3>Update</h3>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#order-payment').prop('disabled', true);

        $(document).on('change', '#purchased-orders', function() {
            let payment = $('#purchased-orders option:selected').attr('name');
            if (payment.toUpperCase() != 'DUE')
                $('#order-payment').prop('disabled', true);
            else
                $('#order-payment').prop('disabled', false);
        });


        $(document).on('click', '#update-button', function() {

            if ($(this).val() != 'default' &&
                $('#purchased-orders option:selected').val() != 'default' &&
                $('#order-payment option:selected').val() != 'default') {
                $.ajax({
                    url: "{{ url('/orders/update/" + $(this).val() + "') }}",
                    type: 'POST',
                    datatype: 'json',
                    data: {
                        po_id: $('#purchased-orders option:selected').val(),
                        payment: $('#order-payment option:selected').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    }
                });
            }
        });
    });
</script>

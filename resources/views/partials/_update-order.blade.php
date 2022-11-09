<div class="order-info">
    <link rel="stylesheet" href="{{ asset('css/update-order.css') }}">
    <h2>Update Purchase Order</h2>
    <div class="details">
        <div class="detail">
            <div class="row">
                <span class="material-icons-sharp primary-variant">receipt_long</span>
                <div class="right-side">
                    <h3>
                        <select id="purchase-orders">
                            @unless($purchasedOrders->isEmpty())
                                <option value="default">-- Please Choose one --</option>
                                @foreach ($purchasedOrders as $purchasedOrder)
                                    <option value="{{ $purchasedOrder->id }}">PO #{{ $purchasedOrder->id }}</option>
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
                        <select id="payment">
                            <option value="default">-- Please Choose one --</option>
                            <option id="paid" value="paid">Paid</option>
                            <option id="refunded" value="refunded">Refunded</option>
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

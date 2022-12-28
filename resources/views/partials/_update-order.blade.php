<div class="order-info">
    <link rel="stylesheet" href="{{ asset('css/update-order.css') }}">

    <h2>Update Purchased Orders</h2>
    <div class="details">
        <div class="detail mb-3">
            <div class="d-flex gap-3 ">
                <span class="material-icons-sharp primary-variant">receipt_long</span>
                <div class="right-side">
                    <h3>
                        <select id="purchased-orders">
                            <option value="default">-- Please Choose one --</option>
                            @unless($purchasedOrders->isEmpty())
                                @foreach ($purchasedOrders as $purchasedOrder)
                                    <option value="{{ $purchasedOrder->id }}">
                                        PO #{{ $purchasedOrder->id }}</option>
                                @endforeach
                            @endunless
                        </select>
                    </h3>
                    <small class="text-muted">PO No.</small>
                </div>
                <button class="btn btn-secondary text-white w-100 ms-4 mb-3" id="edit">Edit</button>
            </div>
        </div>
        <div class="detail mb-3">
            <div class="d-flex gap-3">
                <span class="material-icons-sharp primary-variant">store</span>
                <div class="right-side">
                    <h3>
                        <select id="order-addresses">
                            <option value="default">-- Please Choose one --</option>
                        </select>
                    </h3>
                    <small class="text-muted">Delivery Address</small>
                </div>
            </div>
        </div>
        <div class="detail mb-3">
            <div class="d-flex gap-3">
                <span class="material-icons-sharp primary-variant">local_shipping</span>
                <div class="right-side">
                    <h3>
                        <select id="order-suppliers">
                            <option value="default">-- Please Choose one --</option>
                        </select>
                    </h3>
                    <small class="text-muted">Supplier</small>
                </div>
            </div>
        </div>
        <div class="detail mb-3">
            <div class="d-flex gap-3">
                <span class="material-icons-sharp primary-variant">circle</span>
                <div class="right-side">
                    <h3>
                        <select id="order-status">
                            <option value="default">-- Please Choose one --</option>
                            <option id="pending" value="0">Pending</option>
                            <option id="received" value="1">Received</option>
                            <option id="cancelled" value="-1">cancelled</option>
                        </select>
                    </h3>
                    <small class="text-muted">Order Status</small>
                </div>
            </div>
        </div>
        <div class="detail mb-3">
            <div class="d-flex gap-3">
                <span class="material-icons-sharp primary-variant">payments</span>
                <div class="right-side">
                    <h3>
                        <select id="order-payment">
                            <option value="default">-- Please Choose one --</option>
                            <option id="due" value="Due">Due</option>
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
                <button type="button" id="update-order">
                    <span class="material-icons-sharp">publish</span>
                    <h3>Update</h3>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#order-status').prop('disabled', true);
        $('#order-addresses').prop('disabled', true);
        $('#order-suppliers').prop('disabled', true);
        $('#edit').prop('disabled', true);
        $('#order-payment').prop('disabled', true);
        $('#update-order').prop('disabled', true);

        let orders = [];
        let selectedOrder;

        class Order {
            constructor(id, status, supplier, delivery_address, payment, order_amount) {
                this.id = id;
                this.status = status;
                this.supplier = supplier;
                this.delivery_address = delivery_address;
                this.payment = payment;
                this.order_amount = order_amount;
            }
        }

        $.ajax({
            url: '/api/test/orders/index',
            type: 'GET',
            datatype: 'JSON',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                response.orders.forEach(order => {
                    orders.push(new Order(
                        order.id,
                        order.status,
                        order.supplier,
                        order.delivery_address,
                        order.payment,
                        order.order_amount
                    ));
                });

                response.suppliers.forEach(supplier => {
                    $('#order-suppliers').append(
                        '<option value = "' + supplier.id + '">' +
                        supplier.company_name + '</option>'
                    );
                });

                response.addresses.forEach(address => {
                    $('#order-addresses').append(
                        '<option value = "' + address.id + '">' +
                        address.address + '</option>'
                    );
                });
            }
        });

        $(document).on('click', '#edit', function() {
            $('#order-status').prop('disabled', false);
            $('#order-addresses').prop('disabled', false);
            $('#order-suppliers').prop('disabled', false);
            $('#order-payment').prop('disabled', false);
            $('#update-order').prop('disabled', false);
        });

        $(document).on('change', '#purchased-orders', function() {
            if (this.value != 'default') {
                $('#edit').prop('disabled', false);
                selectedOrder = orders.find((order) => order.id == this.value);
                $('#order-payment').val(selectedOrder.payment);
                $('#order-status').val(selectedOrder.status);
                $('#order-addresses').val(selectedOrder.delivery_address);
                $('#order-suppliers').val(selectedOrder.supplier);
            } else {
                $('#edit').prop('disabled', true);
                $('#order-status').prop('disabled', true);
                $('#order-status').val('default')
                $('#order-addresses').prop('disabled', true);
                $('#order-addresses').val('default')
                $('#order-suppliers').prop('disabled', true);
                $('#order-suppliers').val('default')
                $('#order-payment').prop('disabled', true);
                $('#order-payment').val('default')
                $('#update-order').prop('disabled', true);
            }
        });


        $(document).on('click', '#update-order', function() {
            if ($(this).val() != 'default' &&
                $('#purchased-orders option:selected').val() != 'default' &&
                $('#order-payment option:selected').val() != 'default') {
                $.ajax({
                    url: "{{ url('/orders/update/" + $(this).val() + "') }}",
                    type: 'POST',
                    datatype: 'JSON',
                    data: {
                        po_id: $(
                                '#purchased-orders option:selected')
                            .val(),
                        payment: $('#order-payment option:selected')
                            .val(),
                        status: $('#order-status option:selected')
                            .val(),
                        address: $(
                                '#order-addresses option:selected')
                            .val(),
                        supplier: $(
                                '#order-suppliers option:selected')
                            .val(),
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

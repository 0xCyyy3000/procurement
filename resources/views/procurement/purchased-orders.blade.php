<x-layout :section='$section' :purchasedOrders='$purchasedOrders'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="header">
                <h1 id="po-id"></h1>
            </div>
            <div class="body">
                <div class="top">
                    <p>Requisitioned by <span id="maker" class="primary"></span>
                        from <span id="department" class="primary"></span>
                    </p>
                    <small class="text-muted">Evaluated by <span id="evaluator" class="primary"></span></small>
                    <small class="text-muted">on <span id="date"></span></small>
                </div>

                <div class="middle">
                    <div class="container upper d-flex mb-1">
                        <div class="upper-container">
                            <h3>Payment</h3>
                            <p id="payment" style="font-weight: bold"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status" style="font-weight: bold"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Delivery Address</h3>
                            <p id="delivery-address"></p>
                        </div>
                    </div>
                    <div class="container d-flex justify-content-between w-75">
                        <div class="middle-container">
                            <h3>Supplier</h3>
                            <p id="supplier"></p>
                        </div>
                        <div class="middle-container w-25">
                            <h3>Address</h3>
                            <p id="supplier-address"></p>
                        </div>
                        <div class="middle-container">
                            <h3>Contact</h3>
                            <p id="contact-name"></p>
                        </div>
                        <div class="middle-container">
                            <h3>Email</h3>
                            <p id="contact-email"></p>
                        </div>
                        <div class="middle-container">
                            <h3>Phone</h3>
                            <p id="contact-phone"></p>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="items-table">
                            <table>
                                <thead style="background-color: var(--color-primary);" class="text-white">
                                    <th>Item name</th>
                                    <th>Unit</th>
                                    <th>Item price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </thead>
                                <tbody class="table-body" id="table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="order-total">
                    <h2 class="primary">Order Total</h2>
                    <h2 id="order-total" class="total"></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Receive Modal -->
    <div id="receive-modal" class="collect-modal" style="display: none;">
        <!-- Modal content -->
        <div class="collect-modal-content h-50">
            <h2 class="w-100 m-auto text-center">Receiving Ordered Items</h2>
            <h6 class="m-auto w-50 m-auto mt-2 text-center">Select a Purchased Order
                and receive items. <br> Items will be stored in your inventory.</h6>

            <div class="input-group mt-4 mb-3 d-flex rounded">
                <label class="input-group-text text-center bg-dark-variant text-dark p-2" for="inputGroupSelect01">
                    Purchased Orders
                </label>
                <select class="form-select p-2" id="receiving-order">
                    @unless($purchasedOrders->isEmpty())
                        <option value="">Choose one</option>
                        @foreach ($purchasedOrders as $purchasedOrder)
                            @if ($purchasedOrder->status > 0)
                                <option value="{{ $purchasedOrder->id }}">PO
                                    #{{ $purchasedOrder->id }}
                                </option>
                            @endif
                        @endforeach
                    @endunless
                </select>
            </div>

            <div class="input-group mt-3 mb-3 d-flex rounded">
                <label class="input-group-text text-center bg-dark-variant text-dark p-2" for="inputGroupSelect01">
                    Ordered items
                </label>
                <select class="form-select p-2 w-25" id="items" disabled>
                    <option value="">Choose item</option>
                </select>
            </div>
            <div class="input-group rounded mb-4">
                <input type="number" min="1" class="form-control p-2 " id="item-qty" placeholder="Qty"
                    disabled>
                <span class="input-group-text p-2" style="background-color: #f8fafc;" id="out-of-x">out of x</span>
                <button class="btn warning fw-semibold ps-3 pe-3" type="button" id="btn-max"
                    style="background-color: #e9ecef;" disabled>Max</button>
                <button class="btn ps-4 pe-4 w-50 bg-warning-mine fw-semibold text-white" type="button"
                    id="btn-collect" disabled>
                    Collect
                </button>
            </div>

            <div class="action w-100 m-auto p-4 d-flex justify-content-center">
                <button class="go-back w-25"type="button" id="go-back">
                    <h3>Cancel</h3>
                </button>
                <button class="confirm w-25"type="button" disabled>
                    <h3>Confirm</h3>
                </button>
            </div>

            <div class="collections d-none">
                <h2 class="w-100 text-center text-muted mt-4 mb-3">Collected items</h2>
                <div class="table-responsive">
                    <table class="table border-primary w-100 p-5 m-auto text-center">
                        <thead class="sticky-top bg-primary-mine text-white">
                            <tr>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="collected-items-table" class="h-100">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h1>{{ $section['title'] }}</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>

    <div class="items-table"id="items-table">
        <div class="table">
            <table>
                <thead>
                    <th class="p-2">PO No.</th>
                    <th class="p-2">Supplier</th>
                    <th class="p-2">Payment</th>
                    <th class="p-2">Status</th>
                </thead>
                <tbody>
                    @unless($purchasedOrders->isEmpty())
                        @foreach ($purchasedOrders as $purchasedOrder)
                            @php
                                $status = 'Cancelled';
                                if ($purchasedOrder->status == 0) {
                                    $status = 'Pending';
                                } elseif ($purchasedOrder->status == 1) {
                                    $status = 'Received';
                                }
                            @endphp
                            <tr>
                                <td>
                                    {{ $purchasedOrder->id }}
                                </td>
                                <td>
                                    {{ $purchasedOrder->supplier->company_name }}
                                </td>
                                <td style="font-weight: bold">{{ $purchasedOrder->payment }}</td>
                                <td>
                                    <span
                                        @if ($purchasedOrder->status == 0) class="warning"
                                        @elseif($purchasedOrder->status == 1) class="success"
                                        @else class="danger" @endif>
                                        {{ $status }}
                                    </span>
                                </td>
                                <td> <button class="primary view" type="button" value="{{ $purchasedOrder->id }}">
                                        View details
                                    </button>
                                </td>
                                <td> <button class="text-muted print" type="button" value="{{ $purchasedOrder->id }}">
                                        (Print PO)
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endunless
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let orderedItems = [];
            let collectedItems = [];
            class Item {
                constructor(item_id, item, unit_id, unit, qty) {
                    this.item_id = item_id;
                    this.item = item;
                    this.unit_id = unit_id;
                    this.unit = unit;
                    this.qty = qty;
                }
            }

            let table_body = document.getElementById('table-body');
            var view_modal = document.getElementById("view-modal");
            var status = document.getElementById('status');
            var go_back = document.getElementById("go-back");
            var close_copy = document.getElementById("close-copy");
            const receiveModal = document.getElementById("receive-modal");

            go_back.onclick = function() {
                receiveModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == view_modal)
                    view_modal.style.display = "none";
            }

            function numberWithCommas(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function fetchOrderItems() {
                $('#items').empty().append('<option value="">Choose item</option>');
                $.ajax({
                    url: '/api/orders/' + $('#receiving-order').val(),
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        po_id: $('#receiving-order').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $('#items').append(
                                '<option value="all">All items</option>'
                            );
                            response.items.forEach(item => {
                                orderedItems.push(
                                    new Item(
                                        item.item_id,
                                        item.item,
                                        item.unit_id,
                                        item.unit_name,
                                        item.qty
                                    )
                                );
                                $('#items').append(
                                    '<option value="' + item.item_id + '">' +
                                    item.item + ' (' +
                                    item.unit_name + ')' + '</option>'
                                );
                            });
                        }
                    }

                });
            }

            function loadCollectedItems() {
                document.getElementById('collected-items-table').innerHTML = '';
                collectedItems.forEach(element => {
                    let template = `
                        <tr>
                            <td> ${element.item} </td>
                            <td> ${element.unit} </td>
                            <td> ${element.qty}x </td>
                        </tr>
                    `;

                    document.getElementById('collected-items-table').innerHTML += template;
                });

                $('.confirm').prop('disabled', false);
            }

            $(document).on('click', '.order-select', function() {
                console.log(this.value);
            });

            $(document).on('click', '.view', function() {
                $.ajax({
                    url: "{{ url('/orders/select/" + $(this).val() + "') }}",
                    type: "POST",
                    dateType: 'json',
                    data: {
                        po_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table_body.innerHTML = '';

                        $('#po-id').text('Purchase Order No. ' + response.purchaseOrder.id);
                        $('#maker').text(response.purchaseOrder.maker.name);
                        $('#department').text(response.purchaseOrder.maker.department);
                        $('#evaluator').text(response.purchaseOrder.evaluator.name + ' (' +
                            response.purchaseOrder.evaluator.department + ')');
                        $('#date').text(response.purchaseOrder.created_at.replace(/,/g, ''));

                        $('#payment').text(response.purchaseOrder.payment);
                        if (response.purchaseOrder.payment.toUpperCase() == 'PAID')
                            $('#payment').toggleClass('success');
                        else if (response.purchaseOrder.payment.toUpperCase() == 'DUE')
                            $('#payment').toggleClass('danger');
                        else
                            $('payment').toggleClass('danger');


                        if (response.purchaseOrder.status == 0) {
                            $('#status').text("Pending");
                            $('#status').toggleClass('warning');
                        } else if (response.purchaseOrder.status == 1) {
                            $('#status').text("Received");
                            $('status').toggleClass('success');
                        } else {
                            $('#status').text("Cancelled");
                            $('status').toggleClass('danger');
                        }

                        $('#delivery-address').text(response.purchaseOrder.delivery_address
                            .address);
                        $('#supplier').text(response.purchaseOrder.supplier_name);
                        $('#supplier-address').text(response.purchaseOrder.supplier_address);
                        $('#contact-name').text(response.purchaseOrder.contact_name);
                        $('#contact-email').text(response.purchaseOrder.contact_email);
                        $('#contact-phone').text(response.purchaseOrder.contact_phone);

                        $('#order-total').text('₱' + numberWithCommas(response.purchaseOrder
                            .order_amount));

                        response.orderedItems.forEach(element => {
                            let template = `
                                <tr>
                                    <td> ${element.item_name} </td>
                                    <td> ${element.unit_name} </td>
                                    <td> ₱${numberWithCommas(element.price)} </td>
                                    <td> ${element.qty}x </td>
                                    <td> ₱${numberWithCommas(element.total)} </td>
                                </tr>
                            `;

                            table_body.innerHTML += template;
                        });
                    }
                });

                $('.modal').css('display', 'block');
            });

            $(document).on('click', '.go-back', function() {
                $('#receiving-order').val('');
                $('#items').prop('disabled', true);
                $('#items').empty().append('<option value="">Choose item</option>');
                $('#item-qty').prop('disabled', true);
                $('#item-qty').val('');
                $('#out-of-x').prop('disabled', true);
                $('#out-of-x').text('out of x');
                $('#btn-max').prop('disabled', true);
                $('#btn-collect').prop('disabled', true);
                $('.collect-modal-content').removeClass('h-75');
                $('.collect-modal-content').addClass('h-50');
                $('.collections').addClass('d-none');
            });

            $(document).on('click', '.confirm', function() {
                $.ajax({
                    url: '/inventory/receive',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}',
                        po_id: $('#receiving-order').val(),
                        items: collectedItems
                    },
                    success: function(response) {
                        alert('Ordered items were added to inventory.')
                        location.reload();
                    }
                });
            });

            $(document).on('change', '#receiving-order', function() {
                if ($(this).val() != '') {
                    fetchOrderItems();
                    $('#items').prop('disabled', false);
                } else {
                    $('#items').prop('disabled', true);
                    $('#items').empty().append('<option value="">Choose item</option>');
                    $('#item-qty').prop('disabled', true);
                    $('#item-qty').val('');
                    $('#out-of-x').prop('disabled', true);
                    $('#out-of-x').text('out of x');
                    $('#btn-max').prop('disabled', true);
                    $('#btn-collect').prop('disabled', true);
                }
            });

            $(document).on('input', '#item-qty', function() {
                if ($(this).val() < 1) {
                    $(this).val(1);
                }
            });

            $(document).on('click', '#btn-max', function() {
                $('#item-qty').val($(this).val());
            });

            $(document).on('click', '#btn-collect', function() {
                const my_qty = parseInt($('#item-qty').val());
                const max_qty = parseInt($('#btn-max').val());

                if (my_qty <= max_qty || $('#items').val().toUpperCase() == 'ALL') {
                    if (collectedItems.length == 0 || $('#items').val().toUpperCase() == 'ALL') {
                        $('.collect-modal-content').removeClass('h-50');
                        $('.collect-modal-content').addClass('h-75');
                        $('.collections').removeClass('d-none');
                    }
                    const NO_RECORD = -1;
                    const item = orderedItems.find((item) => item.item_id == $(this).val());
                    if (collectedItems.indexOf(item) == NO_RECORD && $(this).val() != 'all') {
                        item.qty = $('#item-qty').val();
                        collectedItems.push(item);
                        loadCollectedItems();
                    } else if ($(this).val() == 'all') {
                        collectedItems = [];
                        orderedItems.forEach(orderedItem => {
                            collectedItems.push(orderedItem)
                        });
                        loadCollectedItems();
                    }
                } else {
                    alert(
                        'Please enter a valid Qty! \nQty must not exceed its maximum number.');
                    $('#item-qty').val(1);
                }
            })

            $(document).on('change', '#items', function() {
                $('#item-qty').val('');
                if ($(this).val() != '') {
                    if ($(this).val().toUpperCase() == 'ALL') {
                        $('#btn-max').prop('disabled', true);
                        $('#out-of-x').text('All items');
                        $('#item-qty').prop('disabled', true);
                        $('#btn-collect').val('all');
                    } else {
                        const selectedItem = orderedItems.find((item) => item.item_id == $(this).val());
                        $('#btn-max').val(selectedItem.qty);
                        $('#btn-collect').val(selectedItem.item_id);
                        $('#out-of-x').text('out of ' + selectedItem.qty);
                        $('#item-qty').prop('disabled', false);
                        $('#item-qty').val(1);
                        $('#btn-max').prop('disabled', false);
                    }
                    $('#btn-collect').prop('disabled', false);
                } else {
                    $('#out-of-x').text('out of x');
                    $('#btn-max').prop('disabled', true);
                    $('#btn-collect').prop('disabled', true);
                }
            });

            $(document).on('click', '.receive', function() {
                $('#receive-modal').css('display', 'flex');
            });

        });
    </script>
</x-layout>

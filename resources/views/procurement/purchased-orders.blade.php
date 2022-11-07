<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
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
                    <div class="row upper">
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status" style="font-weight: bold"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Delivery Address</h3>
                            <p id="delivery-address"></p>
                        </div>
                    </div>
                    <div class="row
                                middle">
                        <div class="middle-container">
                            <h3>Supplier</h3>
                            <p id="supplier"></p>
                        </div>
                        <div class="middle-container">
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

                    <div class="row">
                        <div class="items-table">
                            <table>
                                <thead>
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

    <!-- Copy Modal -->
    <div id="copy-modal" class="copy-modal" style="display: none">
        <!-- Modal content -->
        <div class="copy-modal-content">
            <span class="close-copy" id="close-copy">&times;</span>
            <br> <br>
            <h2>Copy Requisition Items.
                <br>To continue this action, please confirm.
            </h2>
            <p class="reminder">The <b class="primary">items</b> of this requisition <b class="primary">will be
                    copied</b> <br> and you will be redirected to requisition creation page.</p>
            <br><br>
            <div class="action">
                <button class="go-back"type="button" id="go-back">
                    <h3>Go back</h3>
                </button>
                <button class="confirm"type="button">
                    <h3>Confirm</h3>
                </button>
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
                    <th>PO No.</th>
                    <th>Supplier</th>
                    <th>Payment</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    @unless($purchasedOrders->isEmpty())
                        @foreach ($purchasedOrders as $purchasedOrder)
                            @php
                                $status = Str::upper($purchasedOrder->status);
                            @endphp
                            <tr>
                                <td>{{ $purchasedOrder->id }}</td>
                                <td>
                                    {{ $purchasedOrder->supplier_name->company_name }}
                                </td>
                                <td style="font-weight: bold">{{ $purchasedOrder->payment }}</td>
                                <td>
                                    <span
                                        @if ($status == 'PENDING') class="warning"
                                        @elseif($status == 'FULFILLED') class="success"
                                        @elseif($status == 'PARTIALLY FILLED') class="partial"
                                        @else class="danger" @endif>
                                        {{ $purchasedOrder->status }}
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
            let table_body = document.getElementById('table-body');

            var view_modal = document.getElementById("view-modal");
            var span = document.getElementById("close");
            var status = document.getElementById('status');
            var copy_modal = document.getElementById("copy-modal");
            var go_back = document.getElementById("go-back");
            var close_copy = document.getElementById("close-copy");

            span.onclick = function() {
                view_modal.style.display = "none";
            }

            close_copy.onclick = function() {
                copy_modal.style.display = "none";
            }

            go_back.onclick = function() {
                copy_modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == view_modal) view_modal.style.display = "none";
                else if (event.target == copy_modal) copy_modal.style.display = "none";
            }

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
                        console.log(response);
                        table_body.innerHTML = '';

                        $('#po-id').text('Purchase Order No. ' + response.purchaseOrder.id);
                        $('#maker').text(response.purchaseOrder.maker.name);
                        $('#department').text(response.purchaseOrder.maker.department);
                        $('#evaluator').text(response.purchaseOrder.evaluator);
                        $('#date').text(response.purchaseOrder.created_at.replace(/,/g, ''));

                        $('#status').text(response.purchaseOrder.status);
                        if (response.purchaseOrder.status.toUpperCase() == "CANCELLED")
                            status.style.color = '#ff7782';
                        else if (response.purchaseOrder.status.toUpperCase() ==
                            "FULFILLED")
                            status.style.color = '#41f1b6';
                        else if (response.purchaseOrder.status.toUpperCase() ==
                            "PARTIALLY FULFILLED") status.style.color = '#ccd725';
                        else status.style.color = '#ffbb55';

                        $('#delivery-address').text(response.purchaseOrder.delivery_address);
                        $('#supplier').text(response.purchaseOrder.supplier_name);
                        $('#supplier-address').text(response.purchaseOrder.supplier_address);
                        $('#contact-name').text(response.purchaseOrder.contact_name);
                        $('#contact-email').text(response.purchaseOrder.contact_email);
                        $('#contact-phone').text(response.purchaseOrder.contact_phone);

                        response.orderedItems.forEach(element => {
                            let template = `
                                <tr>
                                    <td> ${element.item_name} </td>
                                    <td> ${element.unit_name} </td>
                                    <td> ₱${element.price} </td>
                                    <td> ${element.qty}x </td>
                                    <td> ₱${element.total} </td>
                                </tr>
                            `;

                            table_body.innerHTML += template;
                        });

                        $('#order-total').text('₱' + response.purchaseOrder.order_total);
                    }
                });

                $('.modal').css('display', 'block');
            });
        });
    </script>
</x-layout>

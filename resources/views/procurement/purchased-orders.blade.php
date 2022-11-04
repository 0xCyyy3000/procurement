<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
            <div class="header">
                <h1 id="req-no">Purchase Order No. ###</h1>
            </div>
            <div class="body">
                <div class="top">
                    <p>Requisitioned by <span id="maker" class="primary"> Chingchong</span>
                        from <span id="department" class="primary">Gen Ed Department</span>
                    </p>
                    <small class="text-muted">Evaluated by <span class="primary">Admin</span></small>
                    <small class="text-muted">on <span id="date">Wed Nov 8 2022</span></small>
                </div>

                <div class="middle">
                    <div class="row upper">
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status" style="font-weight: bold">
                                Pending
                            </p>
                        </div>
                        <div class="upper-container">
                            <h3>Delivery Address</h3>
                            <p id="delivery-address">
                                ACLC Tacloban Real Street
                            </p>
                        </div>
                    </div>
                    <div class="row middle">
                        <div class="middle-container">
                            <h3>Supplier</h3>
                            <p id="supplier">
                                John Doe
                            </p>
                        </div>
                        <div class="middle-container">
                            <h3>Address</h3>
                            <p id="contact-address">
                                124 Jed Harbors Suite 010
                                Klingberg
                            </p>
                        </div>
                        <div class="middle-container">
                            <h3>Contact</h3>
                            <p id="contact-name">John Doe</p>
                        </div>
                        <div class="middle-container">
                            <h3>Email</h3>
                            <p id="contact-email">
                                johndoe@mail.com
                            </p>
                        </div>
                        <div class="middle-container">
                            <h3>Phone</h3>
                            <p id="contact-phone">
                                +639123456789
                            </p>
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
                                <tbody class="table-body" id="table-body">
                                    <tr>
                                        <td>Test item</td>
                                        <td>pcs</td>
                                        <td>₱16.75</td>
                                        <td>32x</td>
                                        <td>₱536</td>
                                    </tr>
                                    <tr>
                                        <td>Test item</td>
                                        <td>pcs</td>
                                        <td>₱16.75</td>
                                        <td>32x</td>
                                        <td>₱536</td>
                                    </tr>
                                    <tr>
                                        <td>Test item</td>
                                        <td>pcs</td>
                                        <td>₱16.75</td>
                                        <td>32x</td>
                                        <td>₱536</td>
                                    </tr>
                                    <tr>
                                        <td>Test item</td>
                                        <td>pcs</td>
                                        <td>₱16.75</td>
                                        <td>32x</td>
                                        <td>₱536</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="order-total">
                    <h2 class="primary">Order Total</h2>
                    <h2 class="total">₱123456789</h2>
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
                    @php
                        $index = 0;
                    @endphp
                    @unless($purchasedOrders->isEmpty())
                        @foreach ($purchasedOrders as $purchasedOrder)
                            @php
                                $status = Str::upper($purchasedOrder->status);
                            @endphp
                            <tr>
                                <td>{{ $purchasedOrder->id }}</td>
                                <td>
                                    @php
                                        if ($suppliers[$index]->id == $purchasedOrder->supplier) {
                                            echo $suppliers[$index]->company_name;
                                        }
                                        $index++;
                                    @endphp
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
            var view_modal = document.getElementById("view-modal");
            var span = document.getElementById("close");
            var status = document.getElementById('status');
            var table_body = document.getElementById('table-body');
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
                $('.modal').css('display', 'block');
            });
        });
    </script>
</x-layout>

<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content w-50" style="margin: 15% auto !important;">
            <input id="supplier" class="text-center fs-2 m-auto">
            <h2 id="supplier-id" class="text-center fs-5 text-muted mb-4"></h2>
            <div class="body">
                <div class="middle">
                    <div class="col-md-fluid">
                        <div class="d-flex gap-3 mb-3 align-items-center ">
                            <h2 class="fs-5 text-muted">Company Address: </h2>
                            <input type="text" class="w-75 fs-5" id="company-address">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center ">
                            <label class="fs-5 text-muted">Email: </label>
                            <input type="email" class="fs-5 w-50" id="company-email">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center ">
                            <h2 class="fs-5 text-muted">Contact Person: </h2>
                            <input type="text" class="fs-5 w-50" id="contact-person">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center ">
                            <h2 class="fs-5 text-muted">Phone Number: </h2>
                            <input type="number" min="0" class="fs-5 w-50" id="phone-number">
                        </div>
                        <div class="d-flex gap-3 mb-3 justify-content-center align-items-center w-50 m-auto">
                            <button class="btn btn-secondary p-2 w-25" id="close-edit">Close</button>
                            <button class="btn bg-primary-mine p-2 text-white" id="save-changes">Save changes</button>
                        </div>
                    </div>

                    <div class="container-fluid visually-hidden">
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
        </div>
    </div>

    <!-- Copy Modal -->
    <div id="copy-modal" class="copy-modal d-none">
        <!-- Modal content -->
        <div class="copy-modal-content" style="min-width: 20% !important; width: 25% !important;">
            <span class="close-copy" id="close-copy"></span>
            <h2 class="mt-2 fs-3 text-center">Removing Supplier</h2>
            <h3 class="reminder text-muted mt-4 fs-5 text-center"><b class="danger">Removing</b> this supplier cannot be
                undone.
                <br>Please press confirm to proceed.
            </h3>
            <br><br>
            <div class="action d-flex gap-2 justify-content-center">
                <button class="w-25"type="button" id="go-back" style="background-color:var(--color-dark);">
                    Go Back
                </button>
                <button class="w-25"type="button" id="confirm" style="background-color:var(--color-primary);">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <h1>{{ $section['title'] }}</h1>
    <div class="date bg-transparent mb-3">
        <input type="hidden" name="" value="">
    </div>

    <div class="items-table" id="items-table">
        <div class="table">
            <table>
                <thead>
                    <th class="p-2">Supplier ID</th>
                    <th class="p-2">Supplier</th>
                    <th class="p-2 w-25">Email</th>
                    <th class="p-2">Address</th>
                </thead>
                <tbody>
                    @unless($suppliers->isEmpty())
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>
                                    {{ $supplier->id }}
                                </td>
                                <td>
                                    {{ $supplier->company_name }}
                                </td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td>
                                    <button class="primary view" type="button" value="{{ $supplier->id }}">
                                        View details
                                    </button>
                                </td>
                                <td>
                                    <button class="danger remove" type="button" value="{{ $supplier->id }}">
                                        Remove
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
            let view_modal = document.getElementById("view-modal");
            let close = document.getElementById('close-edit');
            let remove_modal = document.getElementById('copy-modal');

            close.onclick = function() {
                view_modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == view_modal)
                    view_modal.style.display = "none";
                else if (event.target == remove_modal) {
                    $('.copy-modal').removeClass('d-block');
                    $('.copy-modal').addClass('d-none');
                }
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

            $(document).on('click', '#save-changes', function() {
                $.ajax({
                    url: "{{ url('/suppliers/update/"+$(".view").val()+"') }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}',
                        supplier_id: $('.view').val(),
                        company_name: $('#supplier').val(),
                        contact_person: $('#contact-person').val(),
                        email: $('#company-email').val(),
                        phone: $('#phone-number').val(),
                        address: $('#company-address').val()
                    },
                    success: function(response) {
                        if (response.status == 200)
                            location.reload();
                    }
                });
            });

            $(document).on('click', '.view', function() {
                $.ajax({
                    url: "{{ url('/suppliers/select/" + $(this).val() + "') }}",
                    type: "GET",
                    dateType: 'JSON',
                    data: {
                        supplier_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#supplier').val(response.supplier.company_name);
                        $('#supplier-id').text('Supplier ID: ' + response.supplier.id);
                        $('#company-address').val(response.supplier.address);
                        $('#company-email').val(response.supplier.email);
                        $('#contact-person').val(response.supplier.contact_person);
                        $('#phone-number').val(response.supplier.phone);
                    }
                });

                $('.modal').css('display', 'block');
            });

            $(document).on('click', '.remove', function() {
                $('.copy-modal').removeClass('d-none');
                $('.copy-modal').addClass('d-block');
            });

            $(document).on('click', '#go-back', function() {
                $('.copy-modal').removeClass('d-block');
                $('.copy-modal').addClass('d-none');
            });

            $(document).on('click', '#confirm', function() {
                $.ajax({
                    url: "{{ url('/suppliers/destroy/"+$(".remove").val()+"') }}",
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        supplier_id: $('.remove').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        response.status == 200 ? alert('Removed successfully!') : alert(
                            'Unable to remove, please try again later.');
                        location.reload();
                    }
                });
            });

        });
    </script>
</x-layout>

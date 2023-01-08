<x-layout :section='$section' :distributions='$distributions' :recipients='$recipients' :delivery_address='$addresses'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/distributions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close absolute right-0" id="close"></span>
            <div class="header">
                <h1 id="distribution-no"></h1>
            </div>
            <div class="body">
                <div class="middle position-relative">
                    <div class="d-flex gap-5 mt-3 align-items-center">
                        <div class="mb-2">
                            <h3>Recipient </h3>
                            <p id="recipient"></p>
                        </div>
                        <div class="mb-2">
                            <h3>Department </h3>
                            <p id="recipient-department"></p>
                        </div>
                        <div class="mb-2">
                            <h3>Address </h3>
                            <p id="address"></p>
                        </div>
                        <div class="mb-2">
                            <h3>Distributed on </h3>
                            <p class="text-muted"> <span id="date"></span></p>
                        </div>
                    </div>

                    <div class="table-responsive mt-5">
                        <table class="table text-center">
                            <thead style="background-color: var(--color-primary);" class="text-white sticky-top">
                                <th>Item name</th>
                                <th>Unit</th>
                                <th>Qty</th>
                            </thead>
                            <tbody class="table-body" id="table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="footer"></div>
        </div>
    </div>

    <!-- Copy Modal -->
    <div id="copy-modal" class="copy-modal d-none">
        <!-- Modal content -->
        <div class="copy-modal-content" style="min-width: 20% !important; width: 25% !important;">
            <span class="close-copy" id="close-copy"></span>
            <h2 class="mt-2 fs-3 text-center">Removing Item</h2>
            <h3 class="reminder text-muted mt-4 fs-5 text-center"><b class="danger">Removing</b> this item cannot be
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
        {{-- <input type="hidden" name="" value=""> --}}
    </div>

    <div class="items-table" id="items-table">
        <div class="table">
            <table>
                <thead>
                    <th class="p-2">Distribution No.</th>
                    <th class="p-2">Recipient</th>
                    <th class="p-2 removable">Address</th>
                </thead>
                <tbody>
                    @unless($distributions->isEmpty())
                        @foreach ($distributions as $distribution)
                            <tr>
                                <td>{{ $distribution->id }}</td>
                                <td>{{ $distribution->email }}</td>
                                <td class="removable">{{ $distribution->address }}</td>
                                <td>
                                    <button class="primary view" type="button" value="{{ $distribution->id }}">
                                        View details
                                    </button>
                                </td>
                                <td>
                                    <button class="text-muted copy edit-content"data-bs-toggle="offcanvas"
                                        data-bs-target="#editOffcanvasRight" aria-controls="offcanvasRight"
                                        value="{{ $distribution->id }}">
                                        <span>(Edit Content)</span>
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    @endunless
                </tbody>
            </table>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="editOffcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header p-2">
            <h1 class="offcanvas-title ps-2" id="offcanvasRightLabel">Editing Content</h1>
            <button type="button" class="btn bg-warning-mine text-white text-center py-2 px-3"
                id="edit-content-add">Add item</button>
            <button type="button" class="btn-close pe-3 me-3" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3 overflow-scroll scrollbar-hidden-x">
            <div class="mb-3">
                <h3 class="form-label mb-2 fs-5">Distribution No.</h3>
                <select name="distribution" id="distribution-select" class="p-2 w-100 rounded">
                    <option value="">. </option>
                </select>
            </div>
            <div class="edit-contents d-none">
                <div class="mb-3">
                    <h3 class="form-label mb-2 fs-5">Item</h3>
                    <select name="item" id="edit-item" class="p-2 w-100 rounded">
                        <option value="">. </option>
                    </select>
                </div>
                <div class="mb-3">
                    <h3 class="form-label mb-2 fs-5">Unit</h3>
                    <select name="unit" id="edit-unit" class="p-2 w-100 rounded">
                        <option value="">. </option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h3 class="form-label fs-5">Quantity</h3>
                        <h4 class="text-muted" id="edit-max"></h4>
                    </div>
                    <input type="number" id="edit-qty" min="1" name="qty" class="form-control p-2"
                        placeholder="How many?">
                </div>
                <div class="mb-3 d-flex justify-content-center mt-4">
                    <button class="btn bg-primary-mine d-flex gap-2 align-items-center p-2 " id="edit-save-changes"
                        type="button" disabled>
                        <span class="material-icons-sharp text-white">edit</span>
                        <h3 class="text-white">Save changes</h3>
                    </button>
                </div>
            </div>
            <div class="add-contents d-none">
                <div class="mb-3">
                    <h3 class="form-label mb-2 fs-5">Inventory No.</h3>
                    <select name="inventory" id="edit-add-inventory" class="p-2 w-100 rounded">
                        <option value=""> -- Please Choose One --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <h3 class="form-label mb-2 fs-5">Item</h3>
                    <select name="item" id="edit-add-item" class="p-2 w-100 rounded">
                        <option value="">Please choose Inventory No. </option>
                    </select>
                </div>
                <div class="mb-3">
                    <h3 class="form-label mb-2 fs-5">Unit</h3>
                    <select name="unit" id="edit-add-unit" class="p-2 w-100 rounded">
                        <option value="">Please choose Inventory No. </option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h3 class="form-label fs-5">Quantity</h3>
                        <h4 class="text-muted" id="edit-add-max"></h4>
                    </div>
                    <input type="number" id="edit-add-content-qty" min="1" name="qty"
                        class="form-control p-2" placeholder="How many?" disabled>
                </div>
                <button class="m-auto btn bg-primary-mine d-flex gap-2 align-items-center p-2"
                    id="edit-add-content-btn" type="button" disabled>
                    <span class="material-icons-sharp text-white">add</span>
                    <h3 class="text-white">Add to list</h3>
                </button>
            </div>
            <div class="table-responsive mt-2 border-bottom border-secondary border-1 contents d-none">
                <table class="table text-black text-center ">
                    <thead class="sticky-top bg-primary-mine text-white">
                        <tr>
                            <th class="p-1">Item</th>
                            <th class="p-1">Unit</th>
                            <th class="p-1">Qty</th>
                            <th colspan="2" class="p-1">Action</th>
                        </tr>
                    </thead>
                    <tbody id="edit-items-table"></tbody>
                </table>
            </div>

            <div class="mt-5 mb-0 contents d-none">
                <h3 class="form-label mb-2 fs-5">Recipient</h3>
                <select name="recipient" id="edit-recipient" class="p-2 w-100 rounded" required>
                    @unless($recipients->isEmpty())
                        @foreach ($recipients as $recipient)
                            <option value="{{ $recipient->id }}">{{ $recipient->email }}
                                ({{ $recipient->department }})
                            </option>
                        @endforeach
                    @endunless
                </select>
            </div>

            <div class="mt-3 mb-4 contents d-none">
                <h3 class="form-label mb-2 fs-5">Destination</h3>
                <select name="destination" id="edit-destination" class="p-2 w-100 rounded" required>
                    @unless($addresses->isEmpty())
                        @foreach ($addresses as $address)
                            <option value="{{ $address->id }}">{{ $address->address }}</option>
                        @endforeach
                    @endunless
                </select>
            </div>

            <button
                class="btn bg-primary-mine contents d-none rounded-5 p-3 w-50 d-flex align-items-center gap-2 justify-content-center m-auto p-2"
                type="button" id="submit-changes">
                <span class="material-icons-sharp text-white">send</span>
                <h3 class="text-white fs-6">Submit Changes</h3>
            </button>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            let table_body = document.getElementById('table-body');
            let view_modal = document.getElementById("view-modal");
            let remove_modal = document.getElementById('copy-modal');

            let inventoryItems = [];
            let items = [];
            let selectedItem;
            let distribution;
            let edit_max = 0;
            let is_editing = false;

            class Item {
                constructor(id, item, item_id, unit, unit_id, qty) {
                    this.inventory_id = id;
                    this.item = item;
                    this.item_id = item_id;
                    this.unit = unit;
                    this.unit_id = unit_id;
                    this.qty = qty;
                }
            }

            class Distribution {
                constructor(id, address_id, address, user_id, recipient, department) {
                    this.id = id;
                    this.address_id = address_id;
                    this.address = address;
                    this.user_id = user_id;
                    this.recipient = recipient;
                    this.department = department;
                }
            }

            window.onclick = function(event) {
                if (event.target == view_modal)
                    view_modal.style.display = "none";
                else if (event.target == remove_modal) {
                    $('.copy-modal').removeClass('d-block');
                    $('.copy-modal').addClass('d-none');
                }
            }

            $.ajax({
                url: "{{ url('/inventory/index') }}",
                method: 'GET',
                dataType: 'JSON',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    response.items.forEach(item => {
                        if (item.stock > 0) {
                            inventoryItems.push(new Item(item.id, item.item, item.item_id,
                                item.unit_name, item.unit_id, item.stock));

                            $('#edit-add-inventory').append('<option value="' + item.id +
                                '"> Inventory No. ' + item.id + '</option>');
                        }
                    });
                }
            });

            function loadItems() {
                $('.edit-contents').addClass('d-none');
                document.getElementById('edit-items-table').innerHTML = '';
                items.forEach(item => {
                    let template = `
                        <tr>
                            <td> ${item.item} </td>
                            <td> ${item.unit} </td>
                            <td> ${item.qty}x </td>
                            <td>
                                <button class="primary btn edit-content-item" value="${item.inventory_id}" type="button"> Edit <button>
                            </td>
                            <td>
                                <button class="danger btn remove-content-item" value="${item.inventory_id}" type="button"> Remove <button>
                            </td>
                        </tr>
                    `;
                    document.getElementById('edit-items-table').innerHTML += template;
                });
                $('#edit-item').empty().append('<option value=""></option>');
                $('#edit-unit').empty().append('<option value=""></option>');
                $('#edit-qty').val('');
            }

            $(document).on('click', '#edit-add-content-btn', function() {
                if ($('#edit-add-content-qty').val() <= 0 || $('#edit-add-content-qty').val() > edit_max) {
                    alert('Invalid Quantity input!');
                } else {
                    const item = items.find((item) => item.inventory_id == selectedItem.inventory_id);
                    const invItem = inventoryItems.find((invItem) => invItem.inventory_id == selectedItem
                        .inventory_id);
                    invItem.qty = invItem.qty - parseInt($('#edit-add-content-qty').val());
                    inventoryItems[inventoryItems.indexOf(invItem)] = invItem;
                    if (items.indexOf(item) == -1) {
                        items.push(
                            new Item(selectedItem.inventory_id, selectedItem.item,
                                selectedItem.item_id, selectedItem.unit, selectedItem.unit_id,
                                parseInt($('#edit-add-content-qty').val())
                            )
                        );
                    } else
                        items[items.indexOf(item)].qty += parseInt($('#edit-add-content-qty').val());
                    loadItems();

                    $('#edit-add-item').empty().append(
                        '<option value="">Please choose Inventory No.</option>');
                    $('#edit-add-unit').empty().append(
                        '<option value="">Please choose Inventory No.</option>');
                    $('#edit-add-content-qty').val('');
                    $('#edit-add-inventory').val('');
                    $('#edit-add-max').text('');
                    $('.add-contents').addClass('d-none');
                }
            });

            $(document).on('input', '#edit-add-content-qty', function() {
                $('#edit-add-content-btn').prop('disabled', false);
                if (this.value < 0)
                    this.value = 1;
            });

            $(document).on('change', '#edit-add-inventory', function() {
                if ($(this).val() != '') {
                    $('#edit-add-content-qty').prop('disabled', false);
                    const item = inventoryItems.find((item) => item.inventory_id == $(this).val());
                    console.log(inventoryItems.indexOf(item));
                    if (inventoryItems.indexOf(item) > -1) {
                        $('#edit-add-item').empty().append('<option value="' + item.item_id + '">' + item
                            .item + '</option>');
                        $('#edit-add-unit').empty().append('<option value="' + item.unit_id + '">' + item
                            .unit + '</option>');
                        $('#edit-add-max').text('(Available: ' + item.qty + ')');
                        edit_max = item.qty;
                        selectedItem = item;
                    }
                } else {
                    $('#edit-add-item').empty().append(
                        '<option value=""">Please choose Inventory No. </option>');
                    $('#edit-add-unit').empty().append(
                        '<option value=""">Please choose Inventory No. </option>');
                    $('#edit-add-max').text('hello');
                    $('#edit-add-qty').val('');
                    $('#edit-add-qty').prop('disabled', true);
                }
            });

            $(document).on('click', '.edit-content', function() {
                $.ajax({
                    url: "{{ url('/distributions/edit/" + $(this).val() + "') }}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        distribution_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        items = [];
                        response.items.forEach(item => {
                            items.push(
                                new Item(item.inventory_id, item.item, item.item_id,
                                    item.unit_name, item.unit_id, item.qty)
                            );
                        });
                        console.table(items);

                        inventoryItems = [];
                        response.inventoryItems.forEach(item => {
                            inventoryItems.push(
                                new Item(item.id,
                                    item.item, item.item_id,
                                    item.unit_name, item.unit_id, item.stock)
                            );
                        });

                        distribution = new Distribution(response.distribution[0].id,
                            response.recipient[0].address_id, response.recipient[0].address,
                            response.recipient[0].user_id, response.recipient[0].name,
                            response.recipient[0].department);

                        $('.contents').removeClass('d-none');
                        $('#distribution-select').empty().append(
                            '<option value=""> Distribution No.' +
                            distribution.id + '</option>');

                        loadItems();

                        $('#edit-recipient').val(distribution.user_id);
                        $('#edit-destination').val(distribution.address_id);
                    }
                });
            });

            $(document).on('click', '.edit-content-item', function() {
                $('.add-contents').addClass('d-none');
                $('.edit-contents').removeClass('d-none');

                is_editing = !is_editing;

                selectedItem = items.find((item) => item.inventory_id == $(this).val());
                const invItem = inventoryItems.find((item) => item.inventory_id == selectedItem
                    .inventory_id);

                if ($('#edit-item').val() != selectedItem.inventory_id && $('#edit-unit').val() !=
                    selectedItem.inventory_id) {
                    $('.edit-contents').removeClass('d-none');
                    $('#edit-item').empty().append('<option value="' + selectedItem.inventory_id + '">' +
                        selectedItem.item + '</option>');
                    $('#edit-unit').empty().append('<option value="' + selectedItem.inventory_id + '">' +
                        selectedItem.unit + '</option>');
                    $('#edit-max').text('(Available: ' + (invItem.qty + selectedItem.qty) + ')');
                    edit_max = invItem.qty + selectedItem.qty;
                }
            });

            $(document).on('click', '.remove-content-item', function() {
                selectedItem = items.find((item) => item.inventory_id == $(this).val());
                const invItem = inventoryItems.find((item) => item.inventory_id == selectedItem
                    .inventory_id);

                if (items.length == 1) {
                    alert('Unable to remove, item list should not be empty!');
                } else if (items.indexOf(selectedItem) >= 0) {
                    inventoryItems[inventoryItems.indexOf(invItem)].qty += selectedItem.qty;
                    items.splice(items.indexOf(selectedItem), 1);
                    loadItems();
                }
            });

            $(document).on('input', '#edit-qty', function() {
                $('#edit-save-changes').prop('disabled', false);
            });

            $(document).on('click', '#edit-save-changes', function() {
                const invItem = inventoryItems.find((item) => item.inventory_id == selectedItem
                    .inventory_id);

                if ($('#edit-qty').val() == '') {
                    alert('Quantity cannot be empty!');
                } else if (edit_max == 0 || parseInt($('#edit-qty').val()) > edit_max) {
                    alert('Insufficient inventory stock!');
                } else {
                    items[items.indexOf(selectedItem)].qty = parseInt($('#edit-qty').val());
                    inventoryItems[inventoryItems.indexOf(invItem)].qty = edit_max - parseInt($(
                        '#edit-qty').val());

                    loadItems();
                }
            });

            $(document).on('click', '#submit-changes', function() {
                distribution.address_id = $('#edit-destination').val();
                distribution.user_id = $('#edit-recipient').val();
                $.ajax({
                    url: "{{ url('/distributions/update') }}",
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}',
                        distribution: distribution,
                        items: items,
                        inventory_items: inventoryItems
                    },
                    success: function(response) {
                        if (response == 200) {
                            alert('Changes has been saved!');
                            location.reload();
                        }
                    }
                });
            });

            $(document).on('click', '.view', function() {
                $('.modal').css('display', 'block');
                $.ajax({
                    url: "{{ url('/distributions/select/" + $(this).val() + "') }}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        distribution_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table_body.innerHTML = '';
                        $('#distribution-no').text('Distribution No. ' +
                            response.distribution[0].id);
                        $('#recipient').text(response.recipient[0].email);
                        $('#recipient-department').text(response.recipient[0].department);

                        $('#date').text(
                            // Using RegularExpression to replace ',' (comma) with ' ' (space)
                            // /searchValue/g -> for every instance; g means global
                            // newValue -> the string to replace
                            response.distribution[0].created_at.replace(/,/g, '')
                        );

                        $('#address').text(response.recipient[0].address);
                        $('#description').text(response.distribution[0].description);

                        response.items.forEach(item => {
                            let template = `
                                        <tr>
                                            <td>${item.item}</td>
                                            <td>${item.unit_name}</td>
                                            <td>${item.qty}x</td>
                                        </tr>`;

                            table_body.innerHTML += template;
                        });
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
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
                    url: "{{ url('/inventory/destroy/"+$(".remove").val()+"') }}",
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        inventory_id: $('.remove').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        response.status == 200 ? alert('Removed successfully!') : alert(
                            'Unable to remove, please try again later.');
                        location.reload();
                    }
                });
            });

            $(document).on('click', '#edit-content-add', function() {
                $('.add-contents').removeClass('d-none');
                $('.edit-contents').addClass('d-none');
            });

        });
    </script>
</x-layout>

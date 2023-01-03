<link rel="stylesheet" href="{{ asset('css/distributions.css') }}">
<h2 class="create">Delivery Address Form</h2>
<div class="create-supplier">
    <form action="{{ route('distributions.address.update') }}" method="POST" id="address-form">
        @csrf
        @method('PUT')
        <div class="item create-supplier mb-0">
            <div class="right">
                <div class="form-check form-switch mb-4 w-100 ">
                    <input class="form-check-input me-2 ms-5" type="checkbox" id="edit-address">
                    <label class="form-check-label fs-5 ms-2" for="edit-address">Edit Address</label>
                </div>
                <div class="mb-3 d-none selecting" id="select-address">
                    <p class="text-black">Address</p>
                    <select name="address" id="address" class="p-2 w-100 @error('address') is-invalid @enderror">
                        <option value=""> -- Please Choose One --</option>
                        @unless($delivery_address->isEmpty())
                            @foreach ($delivery_address as $address)
                                <option value="{{ $address->id }}">{{ $address->address }}</option>
                            @endforeach
                        @endunless
                    </select>
                    @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address-input" class="form-label text-black" id="address-label">New
                        Address</label>
                    <input type="text" class="form-control p-2 @error('address-input') is-invalid @enderror"
                        name="address_input" id="address-input" placeholder="Bldg, Street, City" required>
                    @error('address-input')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="item submit">
                <button id="save" class="bg-success-mine" form="address-form" type="submit" name="">
                    <span class="material-icons-sharp">save</span>
                    <h3 id="save-label">Save Address</h3>
                </button>
            </div>
        </div>

    </form>
    <div class="item submit">
        <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
            aria-controls="offcanvasRight">
            <span class="material-icons-sharp">add_circle</span>
            <h3>Distrubte Items</h3>
        </button>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header p-2">
        <h1 class="offcanvas-title ps-2" id="offcanvasRightLabel">Distributing Items</h1>
        <button type="button" class="btn-close pe-3" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-scroll scrollbar-hidden-x">
        <div class="mb-3">
            <h3 class="form-label mb-2 fs-5">Inventory No.</h3>
            <select name="inventory" id="inventory" class="p-2 w-100 rounded">
                <option value=""> -- Please Choose One --</option>
            </select>
        </div>
        <div class="mb-3">
            <h3 class="form-label mb-2 fs-5">Item</h3>
            <select name="item" id="item" class="p-2 w-100 rounded">
                <option value="">Please choose Inventory No. </option>
            </select>
        </div>
        <div class="mb-3">
            <h3 class="form-label mb-2 fs-5">Unit</h3>
            <select name="unit" id="unit" class="p-2 w-100 rounded">
                <option value="">Please choose Inventory No. </option>
            </select>
        </div>
        <div class="mb-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <h3 class="form-label fs-5">Quantity</h3>
                <h4 class="text-muted" id="max"></h4>
            </div>
            <input type="number" id="qty" min="1" name="qty" class="form-control p-2"
                placeholder="How many?">
        </div>
        <div class="mb-3 d-flex justify-content-center mt-4">
            <button class="btn bg-primary-mine d-flex gap-2 align-items-center decision p-2" id="add"
                type="button">
                <span class="material-icons-sharp text-white">add</span>
                <h3 class="text-white">Add to list</h3>
            </button>
            <button class="btn bg-primary-mine d-flex gap-2 align-items-center decision p-2 d-none " id="edit"
                type="button">
                <span class="material-icons-sharp text-white">edit</span>
                <h3 class="text-white">Save changes</h3>
            </button>
        </div>

        <div class="table-responsive mt-2 border-bottom border-secondary border-1 ">
            <table class="table text-black text-center ">
                <thead class="sticky-top bg-primary-mine text-white">
                    <tr>
                        <th class="p-1">Item</th>
                        <th class="p-1">Unit</th>
                        <th class="p-1">Qty</th>
                        <th colspan="2" class="p-1">Action</th>
                    </tr>
                </thead>
                <tbody id="items"></tbody>
            </table>
        </div>

        <div class="mt-5 mb-0">
            <h3 class="form-label mb-2 fs-5">Recipient</h3>
            <select name="recipient" id="add-recipient" class="p-2 w-100 rounded" required>
                <option value=""> -- Please Choose One --</option>
                @unless($recipients->isEmpty())
                    @foreach ($recipients as $recipient)
                        <option value="{{ $recipient->id }}">{{ $recipient->email }}
                            ({{ $recipient->department }})
                        </option>
                    @endforeach
                @endunless
            </select>
        </div>

        <div class="mt-3 mb-4">
            <h3 class="form-label mb-2 fs-5">Destination</h3>
            <select name="destination" id="add-destination" class="p-2 w-100 rounded" required>
                <option value=""> -- Please Choose One --</option>
                @unless($delivery_address->isEmpty())
                    @foreach ($delivery_address as $address)
                        <option value="{{ $address->id }}">{{ $address->address }}</option>
                    @endforeach
                @endunless
            </select>
        </div>

        <button
            class="btn bg-primary-mine rounded-5 p-3 w-50 d-flex align-items-center gap-2 justify-content-center m-auto p-2"
            type="button" id="submit-record">
            <span class="material-icons-sharp text-white">send</span>
            <h3 class="text-white fs-6">Submit and Distribute</h3>
        </button>
    </div>
</div>

<script>
    $(document).ready(function() {
        var msg = "{{ Session::get('alert') }}";
        var exist = "{{ Session::has('alert') }}";
        if (exist) {
            alert(msg);
        }
        let inventoryItems = [];
        let addedItems = [];
        let selectedItem;
        let max = 0;
        let editing = false;

        class Item {
            constructor(id, item_name, item_id, unit_name, unit_id, stock) {
                this.id = id;
                this.item_name = item_name;
                this.item_id = item_id;
                this.unit_name = unit_name;
                this.unit_id = unit_id;
                this.stock = stock;
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
                    inventoryItems.push(new Item(item.id, item.item, item.item_id,
                        item.unit_name, item.unit_id, item.stock));

                    $('#inventory').append('<option value="' + item.id +
                        '"> Inventory No. ' + item.id + '</option>');
                });
            }
        });


        $(document).on('click', '#edit-address', function() {
            $('#select-address').toggleClass('d-none');
            editing = !editing;
            if (editing)
                $('#save-label').text('Save Changes');
            else
                $('#save-label').text('Save Address');
        });

        $(document).on('change', '#address', function() {
            if ($(this).val() != '')
                $('#address-input').val($('#address option:selected').text());
        });

        $('.decision').prop('disabled', true);
        $('#submit-record').prop('disabled', true);

        $(document).on('change', '#inventory', function() {
            if ($(this).val() != '') {
                const item = inventoryItems.find((item) => item.id == $(this).val());
                if (inventoryItems.indexOf(item) >= 0) {
                    $('#item').empty().append('<option value="' + item.item_id + '">' + item
                        .item_name + '</option>');
                    $('#unit').empty().append('<option value="' + item.unit_id + '">' + item
                        .unit_name + '</option>');
                    $('#max').text('(Available: ' + item.stock + ')');
                    max = item.stock;
                    selectedItem = item;
                }
            } else {
                $('#item').empty().append(
                    '<option value=""">Please choose Inventory No. </option>');
                $('#unit').empty().append(
                    '<option value=""">Please choose Inventory No. </option>');
                $('#max').text('');
                $('#qty').val('');
            }
        });

        function populateItems() {
            document.getElementById('items').innerHTML = '';
            addedItems.forEach(item => {
                let template = `
                        <tr>
                            <td> ${item.item_name} </td>
                            <td> ${item.unit_name} </td>
                            <td> ${item.stock}x </td>
                            <td>
                                <button class="primary btn edit-item" value="${item.id}" type="button"> Edit <button>
                            </td>
                            <td>
                                <button class="danger btn remove-item" value="${item.id}" type="button"> Remove <button>
                            </td>
                        </tr>
                    `;
                document.getElementById('items').innerHTML += template;
            });

            $('#inventory').val('');
            $('#item').empty().append(
                '<option value=""">Please choose Inventory No. </option>');
            $('#unit').empty().append(
                '<option value=""">Please choose Inventory No. </option>');
            $('#max').text('');
            $('#qty').val('');
            $('.decision').prop('disabled', true);
        }

        $(document).on('input', '#qty', function() {
            $('.decision').prop('disabled', false);
        });

        $(document).on('click', '.decision', function() {
            const NOT_FOUND = -1;
            const existingItem = addedItems.find((item) => item.id == selectedItem.id);
            if (max == 0 || parseInt($('#qty').val()) > max)
                alert(
                    'INVALID QUANTITY INPUT! \nPlease increase item stocks in inventory if there are not enough available.'
                );
            else if (addedItems.indexOf(existingItem) != NOT_FOUND && $(this).val() != 'edit') {
                alert('Item has already been added!');
                $('#inventory').val('');
                $('#item').empty().append(
                    '<option value=""">Please choose Inventory No. </option>');
                $('#unit').empty().append(
                    '<option value=""">Please choose Inventory No. </option>');
                $('#max').text('');
                $('#qty').val('');
                $('.decision').prop('disabled', true);
            } else {
                const invItem = inventoryItems.find((invItem) => invItem.id == selectedItem.id);
                invItem.stock = invItem.stock - parseInt($('#qty').val());
                inventoryItems[inventoryItems.indexOf(invItem)] = invItem;

                if (addedItems.indexOf(existingItem) == NOT_FOUND) {
                    addedItems.push(
                        new Item(selectedItem.id, selectedItem.item_name, selectedItem.item_id,
                            selectedItem.unit_name, selectedItem.unit_id, parseInt($('#qty').val()))
                    );
                } else {
                    addedItems[addedItems.indexOf(existingItem)].stock = parseInt($('#qty').val());
                    $('#edit').toggleClass('d-none');
                    $('#add').toggleClass('d-none');
                }

                populateItems();
            }

        });

        $(document).on('click', '.edit-item', function() {
            $('#edit').removeClass('d-none');
            $('#add').addClass('d-none');
            const item = addedItems.find((item) => item.id == $(this).val());
            if (addedItems.indexOf(item) >= 0 && $('#inventory').val() == '') {
                $('.decision').val('edit');

                const invItem = inventoryItems.find((invItem) => invItem.id == item.id);
                invItem.stock = invItem.stock + item.stock;
                inventoryItems[inventoryItems.indexOf(invItem)] = invItem;
                $('#max').text('(Available: ' + invItem.stock + ')');
                max = invItem.stock;

                $('#inventory').val(invItem.id);
                $('#item').empty().append('<option value="' + item.item_id + '">' + item
                    .item_name + '</option>');
                $('#unit').empty().append('<option value="' + item.unit_id + '">' + item
                    .unit_name + '</option>');
                $('#max').text('(Available: ' + max + ')');
                $('#qty').val(item.stock);
            } else if ($('#inventory').val() != '' && addedItems.indexOf(item) >= 0)
                $('.decision').val('edit');
            else
                $('.decision').val('add');
        });

        $(document).on('click', '.remove-item', function() {
            const NOT_FOUND = -1;
            const item = addedItems.find((item) => item.id == $(this).val());
            const index = addedItems.indexOf(item)
            if (index != NOT_FOUND) {
                const invItem = inventoryItems.find((invItem) => invItem.id == item.id);
                invItem.stock = invItem.stock + item.stock;
                inventoryItems[inventoryItems.indexOf(invItem)] = invItem;
                addedItems.splice(index, 1);
                populateItems();
            }
        });

        $(document).on('change', '#add-destination', function() {
            if ($('#add-recipient').val() != '' && $(this).val() != '') {
                $('#submit-record').prop('disabled', false);
            } else
                $('#submit-record').prop('disabled', true);
        });

        $(document).on('change', '#add-recipient', function() {
            if ($('#add-destination').val() != '' && $(this).val() != '') {
                $('#submit-record').prop('disabled', false);
            } else
                $('#submit-record').prop('disabled', true);
        });

        $(document).on('click', '#submit-record', function() {
            if (addedItems.length == 0) {
                alert('Please add items to proceed, thank you!');
            } else {
                $.ajax({
                    url: "{{ url('/distributions/create') }}",
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}',
                        items: addedItems,
                        recipient: $('#add-recipient').val(),
                        destination: $('#add-destination').val()
                    },
                    success: function(response) {
                        alert('Successful!');
                        location.reload();
                    }
                });
            }
        });
    });
</script>

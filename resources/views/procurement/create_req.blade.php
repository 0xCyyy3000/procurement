<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/create_req.css') }}">
    <h1>Create Requisition</h1>

    <div class="date-time">
        <div id="date" class="date"></div>
        <div id="time" class="time"></div>
    </div>

    <div class="description">
        <div class="description-content">
            <textarea id="description" placeholder="Describe the purpose" required></textarea>
        </div>
    </div>

    <form method="POST" id="item-form" enctype="multipart/form-data">
        @csrf
        @auth
            <input id="user-id" type="hidden" value="{{ auth()->user()->id }}">
        @endauth
        <div class="wrapper">
            <div class="fields" id="fields">
                <select class="items primary" id="items">
                    <option value="">-- Select one --</option>
                    <option value="new-item">-- New item --</option>
                    @unless($items->isEmpty())
                        @foreach ($items as $item)
                            <option value="{{ $item->item_id }}">{{ $item->item }}</option>
                        @endforeach
                    @endunless
                </select>
                <select id="units">
                    <option value="">-- Select unit --</option>
                </select>
                <input type="text" class="" placeholder="Item name" name="item" id="item"
                    autocomplete="off">
                <input type="text" placeholder="Unit" class="" id="unit" name="unit"
                    autocomplete="off">
                <input type="number" placeholder="How many?" min="1" id="qty" name="qty"
                    autocomplete="off" required>
            </div>
        </div>

        <div class="create">
            <button id="add-button" form="item-form" class="d-flex" type="submit">
                <span id="add-icon" class="material-icons-sharp">add</span>
                <h3>Add item</h3>
            </button>

            <div class="d-none" id="editing">
                <button id="update-button" class="d-flex">
                    <span id="update-icon" class="material-icons-sharp">upgrade</span>
                    <h3>Update Item</h3>
                </button>

                <button id="cancel-edit-btn" type="button" class="danger">
                    <p>Cancel edit</p>
                </button>
            </div>

            <button class="clear-items d-none d-flex" type="button" id="clear-items">
                <span id="trash-icon" class="material-icons-sharp">clear</span>
                <p>Clear items</p>
            </button>
        </div>
    </form>

    <div class="items-table"id="items-table">
        <div class="table">
            <table>
                <thead class="">
                    <th class="p-3">Item name</th>
                    <th>Unit</th>
                    <th>Qty</th>
                </thead>
                <tbody>
                    @unless($savedItems->isEmpty())
                        @php
                            $index = 0;
                        @endphp
                        @foreach ($savedItems as $item)
                            <tr>
                                <td>{{ $item->item }}</td>
                                <td>{{ $units[$index] }}</td>
                                <td>{{ $item->qty }}x</td>
                                <td>
                                    <button type="button" value="{{ $item->row }}" class="edit primary">Edit</button>
                                </td>
                                <td>
                                    <form id="remove-item">
                                        @csrf
                                        <button form="remove-item" class="remove danger" value="{{ $item->row }}">
                                            Remove</button>
                                    </form>
                                </td>
                            </tr>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                    @endunless
                </tbody>
            </table>
        </div>
    </div>

    <script src="{{ asset('js/dateTime.js') }}" defer></script>
    <script>
        let savedItems = '<?php echo $savedItems; ?>';
        var rowIndex = null;
        var IS_ADDING = false;
        var IS_EDITING = false;
        let unitId = null;

        if (savedItems.length > 2)
            $('#clear-items').removeClass('d-none');

        function itemUnits() {
            $.ajax({
                url: "{{ url('/items/units') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#units').html(
                        '<option value="">-- Select unit --</option>');

                    result.forEach(unit => {
                        $("#units").append('<option value="' + unit.unit_id + '">' +
                            unit.unit_name + '</option>');
                    });

                    if ($('#units').length > 0 && IS_EDITING) {
                        $('#units').val(unitId);
                    }
                }
            });
        }

        function getSavedItemByRowID(rowID) {
            $.ajax({
                url: "{{ url('/savedItems/" + rowID + "') }}",
                type: "POST",
                data: {
                    row_id: rowID,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    const item = result[0];
                    if (IS_EDITING) {
                        $('#items').val(item.item_id);
                        unitId = item.unit_id;
                        itemUnits();
                        $('#qty').val(item.qty);
                    }
                }
            });
        }

        $('#item-form').submit(function(e) {

            e.preventDefault();
            var item = $('#items option:selected').text(); // <- From <select/>
            var itemID = $('#items option:selected').val();
            var unit = $('#units').val();
            var url = "{{ url('/items/store/"+item+"') }}";

            if (IS_ADDING) {
                item = $('#item').val();
            } else if (IS_EDITING) {
                url = "{{ url('/savedItems/update/"+itemID+"') }}";
            }

            if (item.toUpperCase() === '' || unit.toUpperCase() === '' ||
                item.toUpperCase() === 'NEW ITEM' || item.toUpperCase() === 'NEW-ITEM') {
                alert('- Please select an Item/Unit\n- Please use a different item name');
                $('#item-form')[0].reset();
                location.reload();
                return;
            }

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item: item,
                    item_id: itemID,
                    qty: $('#qty').val(),
                    unit_id: unit,
                    isAdding: IS_ADDING,
                    row: rowIndex
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status == 200) {
                        if (IS_ADDING) {
                            // Adding the New Added Item to the Select Options for Items
                            $('#items').append('<option value="' + result.item.item_id +
                                '">' + result.item.item + '</option>');

                            IS_ADDING = false;
                        }
                        // Resetting the input fields to default 
                        $('#item-form')[0].reset();
                        $('#units').css('display', 'inline-block');
                        $('#item').css('display', 'none');
                        $('#item').val('');
                        $('#unit').css('display', 'none');
                        $('#unit').val('');

                    } else if (result.update_status == 200) {
                        // Resetting the input fields to default 
                        $('#item-form')[0].reset();
                        IS_EDITING = false;
                        $('#cancel-edit-btn').trigger('click');
                    }

                    $('#items-table').load(location.href + " #items-table");
                    $('#clear-items').removeClass('d-none');
                }
            });

        });

        $('#items').on('change', function(e) {
            e.preventDefault();

            if ($(this).val().toUpperCase() === '') {
                $('#units').html(
                    '<option value="">-- Select unit --</option>');
                $('#units').css('display', 'inline-block');
                $('#item').css('display', 'none');
                $('#unit').css('display', 'none');
                $('#items').removeClass("adding");
                IS_ADDING = false;

                $('#qty').val('');

            } else if ($(this).val().toUpperCase() === 'NEW-ITEM') {
                console.log($(this).val());
                $('#item').css('display', 'inline-block');
                $('#items').toggleClass("adding");
                $('#item').prop('required', true);
                IS_ADDING = true;

                $('#item-form')[0].reset();
                $('#items').val('new-item');
                $('#qty').val('');
                itemUnits();
                $('#units').val('');

            } else {
                $('#items').removeClass("adding");
                $('#units').css('display', 'inline-block');
                $('#item').css('display', 'none');
                $('#unit').css('display', 'none');

                $('#unit').prop('required', false);
                $('#item').prop('required', false);
                IS_ADDING = false;
            }

            if (IS_ADDING || $(this).val() === '') return;
            itemUnits();
        });

        $(document).on('click', '.edit', function() {
            IS_EDITING = true;

            $('#unit').prop('required', false);
            $('#item').prop('required', false);

            if (IS_ADDING) {
                $('#items').removeClass("adding");
                $('#units').css('display', 'inline-block');
                $('#item').css('display', 'none');
                $('#unit').css('display', 'none');
                IS_ADDING = false;
            }

            $('#editing').removeClass("d-none");
            rowIndex = $(this).val();

            getSavedItemByRowID(rowIndex);

            // $('#add-button').css('display', 'none');
            $('#add-button').addClass('d-none');
            $('#update-button').css('display', 'flex');
            $('#cancel-edit-btn').css('display', 'block');
        });

        $(document).on('click', '#cancel-edit-btn', function() {
            IS_EDITING = false;

            $('#add-button').removeClass("d-none");
            $('#editing').addClass("d-none");

            $('#item-form')[0].reset();
        });

        $(document).on('click', '.remove', function() {
            rowIndex = $(this).val();
        });

        $(document).on('submit', '#remove-item', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ url('/savedItems/destroy/"+rowIndex+"') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    row: rowIndex
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status == 200) {
                        if (result.items < 1)
                            $('#clear-items').addClass('d-none')

                        $('#cancel-edit-btn').trigger('click');
                        $('#item-form')[0].reset();

                        $('#items-table').load(location.href + " #items-table");
                    }
                },
                error: function(response) {
                    alert(response.responseJSON.message);
                }
            });
        });

        $(document).on('click', '#clear-items', function() {
            const userId = $('#user-id').val();
            $.ajax({
                url: "{{ url('/savedItems/clear/"+userId+"') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 200) {
                        $('#items-table').load(location.href + " #items-table");
                        $('#clear-items').addClass('d-none');
                        $('#item-form')[0].reset();
                    }
                },
                error: function(response) {
                    alert(response.responseJSON.message);
                }
            });
        });
    </script>

</x-layout>

<link rel="stylesheet" href="{{ asset('css/create-inventory.css') }}">
<h2 class="create">Inventory Form</h2>
<div class="create-supplier">
    <form action="{{ route('inventory.submit-form') }}" method="POST" id="inventory-form">
        @csrf
        <div class="item create-supplier mb-0">
            <div class="right">
                <div class="form-check form-switch mb-4 w-100 ">
                    <input class="form-check-input me-2 decision ms-5" type="checkbox" id="editing">
                    <label class="form-check-label fs-5 ms-2" for="editing">Edit Inventory</label>
                </div>
                <div class="mb-3 d-none selecting" id="select-inventory">
                    <p class="text-black">Inventory No.</p>
                    <select name="inventory_select" id="inventory-select"
                        class="p-2 w-100 @error('inventory_select') is-invalid @enderror">
                    </select>
                    @error('inventory_select')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3" id="select-item">
                    <p class="text-black">Item</p>
                    <select name="item[]" id="items-select" class="p-2 w-100 @error('item[]') is-invalid @enderror">
                        <option value="">-- Please Choose one --</option>
                        @unless($items->isEmpty())
                            @foreach ($items as $item)
                                <option value="{{ $item->item_id }}">{{ $item->item }}</option>
                            @endforeach
                        @endunless
                    </select>
                    <input type="hidden" name="item[]" id="item-input">
                    @error('item[]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3" id="select-unit">
                    <p class="text-black">Unit</p>
                    <select name="unit[]" id="units-select" class="p-2 w-100  @error('unit[]') is-invalid @enderror">
                        <option value="">-- Please Choose one --</option>
                        @unless($units->isEmpty())
                            @foreach ($units as $unit)
                                <option value="{{ $unit->unit_id }}">{{ $unit->unit_name }}</option>
                            @endforeach
                        @endunless
                    </select>
                    <input type="hidden" name="unit[]" id="unit-input">
                    @error('unit[]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="contact-person" class="form-label">Stock</label>
                    <input type="number" min="0" class="form-control p-2 qty @error('qty') is-invalid @enderror"
                        name="qty" placeholder="How many?">
                    @error('qty')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="phone" class="form-label">Price</label>
                    <input type="number" min="1"
                        class="form-control p-2 price @error('price') is-invalid @enderror" name="price"
                        placeholder="How much?">
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="item submit">
            <button id="decision" form="inventory-form" type="submit" name="decision">
                <span class="material-icons-sharp">send</span>
                <h3>Submit</h3>
            </button>
        </div>
    </form>
</div>

<script>
    var msg = "{{ Session::get('alert') }}";
    var exist = "{{ Session::has('alert') }}";
    if (exist) {
        alert(msg);
        location.reload();
    }

    let editing = false;
    let items = [];

    class InventoryItem {
        constructor(id, item_id, item, unit_id, unit, stock, price) {
            this.id = id;
            this.item_id = item_id;
            this.item = item;
            this.unit_id = unit_id;
            this.unit = unit;
            this.stock = stock;
            this.price = price;
        }
    }

    function populateItems() {
        $.ajax({
            url: "{{ url('/inventory/index') }}",
            method: 'GET',
            dataType: 'JSON',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#inventory-select').empty().append(
                    '<option value ="">--Please Choose one--</option>'
                );

                items = [];
                response.items.forEach(item => {
                    $('#inventory-select').append(
                        '<option value ="' + item.id + '">' +
                        'Inventory No. ' + item.id +
                        '</option>'
                    );

                    items.push(
                        new InventoryItem(item.id, item.item_id, item
                            .item, item.unit_id, item.unit, item.stock,
                            item.price
                        )
                    );
                });
            }
        });
    }


    $(document).ready(function() {
        populateItems();

        $('#items-select').prop('required', true);

        $(document).on('click', '#editing', function() {
            editing = !editing;
            $('#decision').val(editing);
            console.log(editing);

            $('#inventory-select').val('');
            $('#items-select').val('');
            $('#unit-input').val('');
            $('#units-select').val('');
            $('.qty').val('');
            $('.price').val('');

            if (editing) {
                $('#select-inventory').toggleClass('d-none');
                $('#inventory-select').prop('required', true);

                $('#items-select').prop('required', false);
                $('#items-select').prop('disabled', true);

                $('#units-select').prop('disabled', true);
            } else {
                $('#inventory-select').prop('required', false);
                $('#select-inventory').toggleClass('d-none');

                $('#items-select').prop('required', true);
                $('#items-select').prop('disabled', false);

                $('#units-select').prop('disabled', false);
            }
        });

        $(document).on('change', '#inventory-select', function() {
            if ($(this).val() != "" && editing) {
                const item = items.find((item) => item.id == $(this).val());
                if (items.indexOf(item) >= 0) {
                    $('#units-select').val(item.unit_id);
                    $('#unit-input').val(item.unit_id);

                    $('#items-select').val(item.item_id);
                    $('#item-input').val(item.item_id);

                    $('.qty').val(item.stock);
                    $('.price').val(item.price);
                }
            } else {
                $('#item-input').val('');
                $('#unit-input').val('');
                $('#items-select').val('');
                $('#units-select').val('');
                $('.qty').val('');
                $('.price').val('');
            }
        });
    });
</script>

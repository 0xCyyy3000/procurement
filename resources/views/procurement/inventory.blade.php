<x-layout :section='$section' :units='$units' :items='$items'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content w-50" style="margin: 15% auto !important;">
            <input id="supplier" class="text-center fs-2 m-auto">
            <h2 id="supplier-id" class="text-center fs-5 text-muted mb-3"></h2>
            <div class="body">
                <div class="middle">
                    <div class="col-md-fluid">
                        <div class="d-flex gap-3 mb-3 align-items-center justify-content-center">
                            <h2 class="fs-5 text-muted">Company Address: </h2>
                            <input type="text" class="fs-5 w-50" id="company-address">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center justify-content-center">
                            <label class="fs-5 text-muted">Email: </label>
                            <input type="email" class="fs-5 w-50" id="company-email">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center justify-content-center">
                            <h2 class="fs-5 text-muted">Contact Person: </h2>
                            <input type="text" class="fs-5 w-50" id="contact-person">
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-center justify-content-center">
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
            <h2 class="mt-2 fs-3 text-center">Removing Item</h2>
            <h3 class="reminder text-muted mt-4 fs-5 text-center"><b class="danger">Removing</b> this item cannot be
                undone. <br>Please press confirm to proceed.
            </h3>
            <p class="text-center text-muted fw-semibold mt-2">(Assigned items cannot be removed)</p>

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

    <div id="new-item" class="add-modal d-none">
        <!-- Modal content -->
        <div class="add-modal-content" style="min-width: 20% !important; width: 25% !important;">
            <span class="close-copy" id="close-copy"></span>
            <h2 class="mt-2 fs-3 text-center" id="add-title"></h2>
            <div class="mb-0 mt-2">
                <label for="adding-new-item" class="form-label mb-2" id="add-label"></label>
                <input type="text" class="form-control p-2 text-center" id="adding-input" placeholder="">
            </div>
            <br><br>
            <div class="action d-flex gap-2 justify-content-center">
                <button class="w-25"type="button" id="go-back" style="background-color:var(--color-dark);">
                    Cancel
                </button>
                <button class="w-25 good"type="button" id="add" style="background-color:var(--color-primary);">
                    Add
                </button>
            </div>
        </div>
    </div>

    <h1>{{ $section['title'] }}</h1>
    <div class="d-flex gap-3 mb-3 mt-3">
        <button class="btn bg-success-mine p-2 ps-4 pe-4 text-white btn-add"
            style="background-color: var(--color-info-dark) !important;" value="item">
            Add new Item </button>
        <button class="btn bg-warning-mine p-2 ps-4 pe-4 text-white btn-add"
            style="background-color: var(--color-info-dark) !important;" value="unit">
            Add new Unit</button>
    </div>

    <div class="items-table" id="items-table">
        <div class="table">
            <table>
                <thead>
                    <th class="p-2">Inventory No.</th>
                    <th class="p-2">Item</th>
                    <th class="p-2">Unit</th>
                    <th class="p-2">Stock</th>
                    <th class="p-2">Price</th>
                </thead>
                <tbody>
                    @unless($inventoryItems->isEmpty())
                        @foreach ($inventoryItems as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->item }}</td>
                                <td>{{ $item->unit_name }}</td>
                                @if ($item->stock == 0)
                                    <td>-</td>
                                @else
                                    <td>{{ $item->stock }}x</td>
                                @endif
                                @if ($item->price == null)
                                    <td>-</td>
                                @else
                                    <td>₱{{ $item->price }}</td>
                                @endif
                                <td>
                                    <button class="danger remove" type="button" value="{{ $item->id }}">
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

    {{ $inventoryItems->links() }}

    <script>
        $(document).ready(function() {

            let table_body = document.getElementById('table-body');
            let view_modal = document.getElementById("view-modal");
            let close = document.getElementById('close-edit');
            let remove_modal = document.getElementById('copy-modal');
            let option = '';

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

            $(document).on('click', '.remove', function() {
                $('.copy-modal').removeClass('d-none');
                $('.copy-modal').addClass('d-block');
            });

            $(document).on('click', '#go-back', function() {
                $('.copy-modal').removeClass('d-block');
                $('.copy-modal').addClass('d-none');

                $('.add-modal').removeClass('d-block');
                $('.add-modal').addClass('d-none');
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

            $(document).on('click', '.btn-add', function() {
                if ($(this).val() == 'unit') {
                    $('#add-title').text('Adding Unit');
                    $('#adding-input').attr('placeholder', 'e.g. Pcs');
                    $('.good').text('Add unit');
                    option = 'unit';
                } else {
                    $('#add-title').text('Adding Item')
                    $('#adding-input').attr('placeholder', 'e.g. Short bond paper');
                    $('.good').text('Add item');
                    option = 'item';
                }
                $('.add-modal').toggleClass('d-none');
                $('.add-modal').toggleClass('d-block');
            });

            $(document).on('click', '#add', function() {
                if ($('#adding-input').val() == '') {
                    alert('Input should not be empty!');
                    location.reload();
                    return;
                }
                $.ajax({
                    url: "{{ url('/inventory/add') }}",
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        _token: '{{ csrf_token() }}',
                        option: option,
                        value: $('#adding-input').val()
                    },
                    success: function(response) {
                        response.status == 200 ? alert('Added successfully!') : '';
                        location.reload();
                    }
                });
            });
        });
    </script>
</x-layout>

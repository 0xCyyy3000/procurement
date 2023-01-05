<x-layout :section='$section' :suppliers='$suppliers' :requisitions='$requisitions' :delivery_address='$delivery_address'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close absolute right-0" id="close"></span>
            <div class="header">
                <h1 id="req-no"></h1>
            </div>
            <div class="body">
                <div class="top">
                    <p>Requisitioned by <span id="maker" class="primary fw-bold"></span>
                        <span id="department" class="text-muted"></span>
                    </p>
                    <p>on <span id="date"></span></p>
                </div>

                <div class="middle">
                    <div class="row">
                        <h3>Description</h3>
                        <p id="description"></p>
                    </div>
                    <div class="container upper">
                        <div class="upper-container">
                            <h3>Priority</h3>
                            <p id="priority" class="primary" style="font-weight: bold"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status" style="font-weight: bold"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Supplier</h3>
                            <p id="supplier" class="" style=""></p>
                        </div>
                        <div class="upper-container">
                            <h3>Evaluator</h3>
                            <p id="req_evaluator"></p>
                        </div>
                    </div>

                    <div class="container">
                        <div class="message">
                            <div class="label">
                                <h3>Message</h3>
                            </div>
                            <div class="content">
                                <p id="message"></p>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="items-table table-responsive">
                            <table class="">
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
            </div>
            <div class="footer"></div>
        </div>
    </div>

    <!-- Copy Modal -->
    <div id="copy-modal" class="copy-modal d-none">
        <!-- Modal content -->
        <div class="copy-modal-content">
            <span class="close-copy" id="close-copy"></span>
            <h2 class="mt-2">Copy Requisition Items.
                <br>To continue this action, please confirm.
            </h2>
            <p class="reminder">The <b class="primary">items</b> of this requisition <b class="primary">will be
                    copied</b> <br> and you will be redirected to requisition creation page.</p>
            <br><br>
            <div class="action d-flex gap-2">
                <button class="go-back w-25"type="button" id="go-back">
                    <h3>Go back</h3>
                </button>
                <button class="confirm w-25"type="button">
                    <h3>Confirm</h3>
                </button>
            </div>
        </div>
    </div>

    <h1>Requisitions</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>

    <div class="items-table"id="items-table">
        <div class="table">
            <table class="">
                <thead>
                    <th class="p-2">Req No.</th>
                    <th class="p-2">Priority</th>
                    <th class="th-description p-2">Description</th>
                    <th class="p-2">Status</th>
                </thead>
                <tbody>
                    @unless($requisitions->isEmpty())
                        @foreach ($requisitions as $requisition)
                            @php
                                $status = Str::upper($requisition->status);
                            @endphp
                            <tr>
                                <td>{{ $requisition->req_id }}</td>
                                <td style="font-weight: bold">{{ $requisition->priority }}</td>
                                <td>{{ $requisition->description }}</td>
                                <td>
                                    <span
                                        @if ($status == 'FOR APPROVAL' or $status == 'PENDING') class="warning"
                                        @elseif($status == 'APPROVED') class="success"
                                        @elseif($status == 'PARTIALLY APPROVED') class="partial"
                                        @else class="danger" @endif>
                                        {{ $requisition->status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="primary view" type="button" value="{{ $requisition->req_id }}">
                                        View details
                                    </button>
                                </td>
                                <td>
                                    <button class="text-muted copy" value="{{ $requisition->req_id }}">
                                        <span>(Copy items)</span>
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

            var msg = "{{ Session::get('alert') }}";
            var exist = "{{ Session::has('alert') }}";
            if (exist) {
                // alert(msg);
                $('#flash-toast-maker').text('You ');
                $('#flash-toast-context').text(msg);
                $('.flash-toast').toast('show');
            }

            var view_modal = document.getElementById("view-modal");
            var span = document.getElementById("close");
            var status = document.getElementById('status');
            var table_body = document.getElementById('table-body');
            var copy_modal = document.getElementById("copy-modal");
            var go_back = document.getElementById("go-back");
            var close_copy = document.getElementById("close-copy");
            var id = null;

            span.onclick = function() {
                view_modal.style.display = "none";
            }

            close_copy.onclick = function() {
                // copy_modal.style.display = "none";
                $('#copy-modal').removeClass('d-block');
                $('#copy-modal').addClass('d-none');
            }

            go_back.onclick = function() {
                $('#copy-modal').removeClass('d-block');
                $('#copy-modal').addClass('d-none');
            }

            window.onclick = function(event) {
                if (event.target == view_modal) view_modal.style.display = "none";

                else if (event.target == copy_modal) {
                    $('#copy-modal').removeClass('d-block');
                    $('#copy-modal').addClass('d-none');
                }
            }

            $(document).on('click', '.view', function() {
                $.ajax({
                    url: "{{ url('/requisitions/" + $(this).val() + "') }}",
                    type: "POST",
                    data: {
                        viewing: true,
                        req_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 200) {
                            table_body.innerHTML = '';
                            $('#req-no').text('Requisition No. ' + response.requisition[0]
                                .req_id);
                            $('#maker').text(response.requisition[0].maker);
                            $('#department').text('(' + response.requisition[0].department +
                                ')');

                            $('#date').text(
                                // Using RegularExpression to replace ',' (comma) with ' ' (space)
                                // /searchValue/g -> for every instance; g means global
                                // newValue -> the string to replace
                                response.requisition[0].created_at.replace(/,/g, '')
                            );

                            $('#priority').text(response.requisition[0].priority);
                            $('#description').text(response.requisition[0].description);
                            $('#status').text(response.requisition[0].status);

                            response.requisition[0].stage == 0 ?
                                $('#supplier').text('Not assigned') :
                                $('#supplier').text(response.requisition[0].company_name);

                            response.requisition[0].evaluator == null ?
                                $('#req_evaluator').text('None yet') :
                                $('#req_evaluator').text(response.evaluator[0].name +
                                    ' (' + response.evaluator[0].department + ')');

                            if (response.requisition[0].message != null) {
                                $('#message').text(response.requisition[0].message);
                                $('.message').css('display', 'block');
                            } else $('.message').css('display', 'none');

                            response.items.forEach(item => {
                                let template = `
                                        <tr>
                                            <td>${item.item}</td>
                                            <td>${item.unit_name}</td>
                                            <td>${item.qty}x</td>
                                        </tr>`;

                                table_body.innerHTML += template;
                            });

                            $('.modal').css('display', 'block');

                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.copy', function() {
                $('.copy-modal').removeClass('d-none');
                $('.copy-modal').addClass('d-block');
                id = this.value;
            })

            $(document).on('click', '.confirm', function() {
                $.ajax({
                    url: "{{ url('/requisitions/copy/"+id+"') }}",
                    type: 'POST',
                    data: {
                        req_id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            alert('Items copied successfully!');
                            location.reload();
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            })
        });
    </script>
</x-layout>

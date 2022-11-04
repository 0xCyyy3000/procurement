<x-layout :section='$section' :suppliers='$suppliers'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">

    <!-- View Modal -->
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
            <div class="header">
                <h1 id="req-no"></h1>
            </div>
            <div class="body">
                <div class="top">
                    <p>Requisitioned by <span id="maker" class="primary"></span>
                        from <span id="department" class="primary"></span>
                    </p>
                    <p>on <span id="date"></span></p>
                </div>

                <div class="middle">
                    <div class="row">
                        <h3>Description</h3>
                        <p id="description"></p>
                    </div>
                    <div class="row upper">
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
                            <h3>School Director's Signatory</h3>
                            <p id="director-approval"></p>
                        </div>
                        <div class="upper-container">
                            <h3>Branch Manager's Signatory</h3>
                            <p id="manager-approval"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="message">
                            <div class="label">
                                <h3>Message</h3>
                            </div>
                            <div class="content">
                                <p id="message"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="items-table">
                            <table>
                                <thead>
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

    <h1>Requisitions</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>

    <div class="items-table"id="items-table">
        <div class="table">
            <table>
                <thead>
                    <th>Req No.</th>
                    <th>Priority</th>
                    <th class="th-description">Description</th>
                    <th>Status</th>
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
                                        @if ($status == 'PENDING') class="warning"
                                        @elseif($status == 'APPROVED') class="success"
                                        @elseif($status == 'PARTIALLY APPROVED') class="partial"
                                        @else class="danger" @endif>
                                        {{ $requisition->status }}
                                    </span>
                                </td>
                                <td> <button class="primary view" type="button" value="{{ $requisition->req_id }}">
                                        View details</button>
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

                            $('.modal').css('display', 'block');
                            $('#req-no').text('Requisition No. ' + response.requisition[0]
                                .req_id);
                            $('#maker').text(response.requisition[0].maker);
                            $('#department').text(response.department[0].department +
                                '  Department');

                            $('#date').text(
                                // Using RegularExpression to replace ',' (comma) with ' ' (space)
                                // /searchValue/g -> for every instance; g means global
                                // newValue -> the string to replace
                                response.requisition[0].created_at.replace(/,/g, '')
                            );

                            $('#priority').text(response.requisition[0].priority);
                            $('#description').text(response.requisition[0].description);
                            $('#status').text(response.requisition[0].status);

                            $('#director-approval').text(
                                response.requisition[0].signatories[0]
                                .approval);

                            $('#manager-approval').text(
                                response.requisition[0].signatories[1]
                                .approval);

                            response.supplier.length > 0 ? $('#supplier').text(response
                                .supplier[0].company_name) : $('#supplier').text(
                                'Not assigned');

                            if (response.requisition[0].message != null) {
                                $('#message').text(response.requisition[0].message);
                                $('.message').css('display', 'block');
                            } else $('.message').css('display', 'none');


                            if (response.requisition[0].status.toUpperCase() == "REJECTED")
                                status.style.color = '#ff7782';
                            else if (response.requisition[0].status.toUpperCase() ==
                                "APPROVED")
                                status.style.color = '#41f1b6';
                            else if (response.requisition[0].status.toUpperCase() ==
                                "PARTIALLY APPROVED") status.style.color = '#ccd725';
                            else status.style.color = '#ffbb55';

                            response.requisitionItems.forEach(element => {
                                let itemName = response.items.find(item => item
                                    .item_id == element.item_id).item;

                                let unitName = response.units.find(unit => unit
                                    .unit_id == element.unit_id).unit_name;

                                let template = `
                                        <tr>
                                            <td>${itemName}</td>
                                            <td>${unitName}</td>
                                            <td>${element.qty}x</td>
                                        </tr>`;

                                table_body.innerHTML += template;
                            });
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.copy', function() {
                $('.copy-modal').css('display', 'block');
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

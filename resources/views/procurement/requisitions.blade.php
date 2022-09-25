<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <div id="view-modal" class="modal" style="display: none">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
            <div class="header">
                <h1 id="req-no"></h1>
            </div>
            <div class="body">
                <div class="top">
                    <p>Requisitioned by <span id="maker" class="primary"></span></p>
                    <p>On <span id="date"></span></p>
                </div>
                <hr>
                <div class="middle">
                    <div class="row">
                        <h3>Description</h3>
                        <p id="description" style="font-size: medium"></p>
                    </div>
                    <div class="row upper">
                        <div class="upper-container">
                            <h3>Priority</h3>
                            <p id="priority" class="primary" style="font-weight: bold">High (Urgent)</p>
                        </div>
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status" style="font-weight: bold">Pending</p>
                        </div>
                        <div class="upper-container">
                            <h3>School Director's Signatory</h3>
                            <p id="sig1-stats" style="font-style: italic">unsigned</p>
                        </div>
                        <div class="upper-container">
                            <h3>Branch Manager's Signatory</h3>
                            <p id="sig2-stats" style="font-style: italic">unsigned</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="items-table">
                            <h3>Submitted Items</h3>
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
    <h1>Requisitions</h1>
    <div class="date">
        <input type="date" name="" value="">
    </div>

    <div class="items-table"id="items-table">
        <table>
            <thead>
                <th>Req No.</th>
                <th>Priority</th>
                <th width="">Description</th>
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
                            <td style="font-weight: bold"
                                @if ($status == 'PENDING') class="warning"
                                    @elseif($status == 'APPROVED') class="success"
                                    @else class="danger" @endif>
                                {{ $requisition->status }}
                            </td>
                            <td> <button class="primary view" type="button" value="{{ $requisition->req_id }}">
                                    View details</button>
                            </td>
                            <td><button class="text-muted copy" value="{{ $requisition->req_id }}">
                                    <span>(Copy items)</span></button></td>
                        </tr>
                    @endforeach
                @endunless
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var view_modal = document.getElementById("view-modal");
            var span = document.getElementById("close");
            var status = document.getElementById('status');
            var table_body = document.getElementById('table-body');

            span.onclick = function() {
                view_modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == view_modal) view_modal.style.display = "none";
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
                        console.log(response);
                        if (response.status == 200) {
                            table_body.innerHTML = '';

                            $('.modal').css('display', 'block');
                            $('#req-no').text('Requisition No. ' + response.requisition[0]
                                .req_id);
                            $('#maker').text(response.requisition[0].maker);
                            $('#date').text(response.requisition[0].created_at);
                            $('#priority').text(response.requisition[0].priority);
                            $('#description').text(response.requisition[0].description);
                            $('#status').text(response.requisition[0].status);

                            if (response.requisition[0].status.toUpperCase() == "REJECTED")
                                status.style.color = '#ff7782';
                            else if (response.requisition[0].status.toUpperCase() == "APPROVED")
                                status.style.color = '#7380ec';
                            else status.style.color = '#ffbb55';

                            const ids = response.items[0].item_ids.split(',');
                            const items = response.items[0].items.split(',');
                            const units = response.items[0].units.split(',');
                            const qtys = response.items[0].qtys.split(',');

                            for (let index = 0; index < items.length; index++) {
                                let template = `
                                <tr>
                                  <td>${items[index]}</td>
                                  <td>${units[index]}</td>
                                  <td>${qtys[index]}x</td>
                                </tr>`;
                                table_body.innerHTML += template;
                            }
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.copy', function() {
                console.log($(this).val());

            })
        });
    </script>
</x-layout>

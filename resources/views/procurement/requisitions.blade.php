<x-layout :section='$section'>
    <link rel="stylesheet" href="{{ asset('css/requisitions.css') }}">
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
                    <p>on <span id="date"></span> at <span id="time"></span></p>
                </div>
                <hr>
                <div class="middle">
                    <div class="row upper">
                        <div class="upper-container">
                            <h3>Priority</h3>
                            <p id="priority">High (Urgent)</p>
                        </div>
                        <div class="upper-container">
                            <h3>Status</h3>
                            <p id="status">Pending</p>
                        </div>
                        <div class="upper-container">
                            <h3>School Director's Approval</h3>
                            <p id="sig1-stats">unsigned</p>
                        </div>
                        <div class="upper-container">
                            <h3>Branch Manager's Approval</h3>
                            <p id="sig2-stats">unsigned</p>
                        </div>
                        <div class="upper-container">
                            <h3>Released</h3>
                            <p id="is-released">False</p>
                        </div>
                    </div>
                    <div class="row">
                        <h3>Description</h3>
                        <p id="description"></p>
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
                            <td>{{ $requisition->priority }}</td>
                            <td>{{ $requisition->description }}</td>
                            <td
                                @if ($status == 'PENDING') class="warning"
                                    @elseif($status == 'APPROVED') class="success"
                                    @else class="danger" @endif>
                                {{ $requisition->status }}
                            </td>
                            <td> <button class="primary view" type="button" value="{{ $requisition->req_id }}">
                                    View details</button>
                            </td>
                            <td><button class="text-muted copy"> <span>(Copy)</span></button></td>
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
                        req_id: $(this).val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response) {
                            console.log(response);
                            $('.modal').css('display', 'block');
                            $('#req-no').text('Req #' + response[0].req_id);
                            $('#maker').text(response[0].maker);
                            $('#date').text(response[0].created_at);
                            $('#priority').text(response[0].priority);
                            $('#status').text(response[0].status);
                            $('#description').text(response[0].description);
                        }
                    }
                });
            });
        });
    </script>
</x-layout>

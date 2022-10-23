<link rel="stylesheet" href="{{ asset('css/update-status.css') }}">
<div class="requisition-info">
    <h2>Update Status</h2>
    <div class="details">
        <form id="update-status-form">
            @csrf
            <div class="detail">
                <div class="row">
                    <span class="material-icons-sharp">request_page</span>
                    <div class="right-side">
                        <h3>
                            <select id=reqs class="primary">
                                <option value="default"> -- Please Choose one -- </option>
                            </select>
                        </h3>
                        <small class="text-muted">Requisition No.</small>
                    </div>
                </div>
            </div>
            <div class="detail">
                <div class="row">
                    <span class="material-icons-sharp">person</span>
                    <div class="right-side">
                        <h3>
                            <select id="signatory" class="primary">
                                <option value="default">-- Please Choose one --</option>
                                <option id="School Director" value="School Director">School Director</option>
                                <option id="Branch Manager" value="Branch Manager">Branch Manager</option>
                                <option id="Both" value="Both">Both Signatories</option>
                            </select>
                        </h3>
                        <small class="text-muted">Signatory</small>
                    </div>
                </div>
            </div>
            <div class="detail">
                <div class="row">
                    <span class="material-icons-sharp">approval</span>
                    <div class="right-side">
                        <h3>
                            <select id="approval" class="primary">
                                <option value="default">-- Please Choose one --</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </h3>
                        <small class="text-muted">Approval</small>
                    </div>
                </div>
            </div>
            <div class="detail">
                <div class="row">
                    <div class="right-side">
                        <h3>
                            <textarea name="" id="requisition-comment" cols="30" rows="2" placeholder="Message (optional)"></textarea>
                        </h3>
                    </div>
                </div>
                <div class="row update">
                    <button type="submit" id="update-button" disabled>
                        <span class="material-icons-sharp">update</span>
                        <h3>Update</h3>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            let message = '';
            let approvalCount = new Array(),
                signatories = new Array();

            $('#signatory').prop('disabled', true);
            $('#approval').prop('disabled', true);

            $.ajax({
                url: "{{ url('/api/get/requisitions') }}",
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    result.forEach(element => {
                        $('#reqs').append('<option value=' + element.req_id + '>' +
                            'Req #' + element.req_id + '</option>')

                        approvalCount.push(element.approval_count);
                        signatories.push(element.signatories)
                    });
                }
            });

            $(document).on('input', '#requisition-comment', function() {
                message = $(this).val();
            })

            $(document).on('change', '#reqs', function(e) {
                $('#approval').val('default');
                $('#signatory').val('default');

                // Formatting the index to start at 0, since it's the rule of thumb
                const selectedIndex = this.selectedIndex - 1;

                if ($(this).val() != 'default' &&
                    (approvalCount[selectedIndex] < 2 && approvalCount[selectedIndex] >= 0)) {

                    $('#signatory').prop('disabled', false);

                    // Disabling Both Option if either of the Signatories has signed already
                    if (approvalCount[selectedIndex] == 0) document.getElementById('Both').disabled = false;
                    else document.getElementById('Both').disabled = true;

                    signatories[selectedIndex].forEach(element => {
                        if (element.approval == 'Not yet')
                            document.getElementById(element.name).disabled = false;
                        else
                            document.getElementById(element.name).disabled = true;
                    });
                } else {
                    $('#update-button').prop('disabled', true);
                    $('#signatory').prop('disabled', true);
                    $('#approval').prop('disabled', true);
                }
            });

            $(document).on('change', '#signatory', function() {
                $('#approval').val('default');

                if ($(this).val() != 'default') $('#approval').prop('disabled', false);
                else $('#approval').prop('disabled', true);
            });

            $(document).on('change', '#approval', function() {
                if ($(this).val() != 'default') $('#update-button').prop('disabled', false);
                else $('#update-button').prop('disabled', true);
            });

            $(document).on('submit', '#update-status-form', function(e) {
                e.preventDefault();
                const reqId = $('#reqs option:selected').val();

                $.ajax({
                    url: "{{ url('/requisitions/update/"+reqId+"') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        req_id: $('#reqs option:selected').val(),
                        signatories: $('#signatory option:selected').val(),
                        approval: $('#approval option:selected').val(),
                        message: message
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            alert("Status has been updated for Req no. " + reqId);
                            location.reload();
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });

            });
        });
    </script>
</div>

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
                                <option value="school-director">School Director</option>
                                <option value="branch-manager">Branch Manager</option>
                                <option value="all-signatories">All Signatories</option>
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
                                <option value="unsigned">-- Please Choose one --</option>
                                <option value="signed">Signed (Approved)</option>
                                <option value="not signed">Not Signed (Rejected)</option>
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
                    <button type="submit" id="update-button">
                        <span class="material-icons-sharp">update</span>
                        <h3>Update</h3>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {

            $.ajax({
                url: "{{ url('/api/get/requisitions') }}",
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    result.forEach(element => {
                        $('#reqs').append('<option value=' + element.req_id + '>' +
                            'Req #' + element.req_id + '</option>')
                    });
                }
            });

            $(document).on('change', '#reqs', function(e) {
                if ($('#reqs option:selected').val() != 'default' || $('#reqs').val() != 'default') {
                    $('#update-button').prop('disabled', false);
                } else {
                    $('#update-button').prop('disabled', true);
                }
                console.log('changed');
                console.log($('#update-button').prop('disabled'));
            });

            $(document).on('click', '#update-button', function(e) {
                if ($('#reqs').val() == 'default' || $('#update-button').prop('disabled') ||
                    $('#reqs option:selected').val() == 'default') {
                    alert('Please choose a Requisition Number!');
                    console.log('clicked!');
                    $('#update-button').prop('disabled', true);
                }
            });

            $(document).on('submit', '#update-status-form', function(e) {
                e.preventDefault();
                const reqId = $('#reqs option:selected').val();
                console.log("HELLO!");
                console.log(reqId);

                // $.ajax({
                //     url: "{{ url('/requisitions/update/"+reqId+"') }}",
                //     type: 'POST',
                //     dataType: 'json',
                //     success: function(response) {
                //         console.log(response);
                //         if (response.status == 200) {
                //             alert("Status has been updated for Req no. " + reqId);
                //         }
                //     },
                //     error: function(response) {
                //         alert(response.responseJSON.message);
                //     }
                // });

            });
        });
    </script>
</div>

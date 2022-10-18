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
                            <select id="approval-1-select" class="primary">
                                <option value="default">-- Please Choose one --</option>
                                <option value="school-director">School Director</option>
                                <option value="branch-manager">Branch Manager</option>
                            </select>
                        </h3>
                        <small class="text-muted">Signatory</small>
                    </div>
                </div>
            </div>
            <div class="detail">
                <div class="row">
                    <span class="material-icons-sharp">person</span>
                    <div class="right-side">
                        <h3>
                            <select id="approval-2-select" class="primary">
                                <option value="unsigned">-- Please Choose one --</option>
                                <option value="signed">Signed (Approved)</option>
                                <option value="not signed">Not Signed (Rejected)</option>
                            </select>
                        </h3>
                        <small class="text-muted">Conclusion</small>
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
                    <button type="submit" form="update-status-form" id="update-signatory">
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

            $('#update-status-form').submit(function(e) {
                e.preventDefault();
                console.log("HELLO!");
            });
        });
    </script>
</div>

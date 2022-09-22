<div id="view-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" id="close">&times;</span>
        <div class="header">
            <h1 id="req-no">{{ Cache::pull('req_id') }}</h1>
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

<script>
    $(document).ready(function() {
        var view_modal = document.getElementById("view-modal");
        var span = document.getElementById("close");

        span.onclick = function() {
            view_modal.parentNode.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == view_modal) view_modal.parentNode.style.display = "none";
        }
    });
</script>

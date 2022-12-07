<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    $(document).ready(function() {
        Pusher.logToConsole = true;

        var pusher = new Pusher('5151487ae3a52c46ef4e', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('requisition-channel');
        channel.bind('requisition-event', function(data) {
            $('#maker').text(JSON.stringify(data.name));
            $('#context').text(JSON.stringify(data.context));
            $('.toast').toast('show');
        });
    })
</script>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast p-3 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto fw-bolder fs-5">Notification</strong>
            <small class="p-2 text-muted">a moment ago</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <p>
                <span id="maker" class="fw-bolder"> Cy pogi </span>
                <span id="context">has submitted a new requisition</span>
            </p>

        </div>
    </div>
</div>

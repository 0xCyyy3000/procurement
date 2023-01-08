<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    $(document).ready(function() {
        Pusher.logToConsole = true;
        const user = '{{ Auth::user()->id }}';
        const department = '{{ Auth::user()->department }}'

        var pusher = new Pusher('5151487ae3a52c46ef4e', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('requisition-channel');
        channel.bind('requisition-event', function(data) {
            console.table(data);
            let mine = false;
            if (data.event == 'CREATE REQ' || data.event == 'UPDATE REQ') {
                if (user == data.id || department <= 3) {
                    if (user == data.id && data.event == 'CREATE REQ') {
                        $('#pusher-maker').text('You');
                    } else if (data.event == 'CREATE REQ' && department <= 3)
                        $('#pusher-maker').text(data.name);
                    else if (data.event == 'UPDATE REQ' && user == data.evaluator['id'] &&
                        user != data.id)
                        $('#pusher-maker').text('You');
                    else if (data.event == 'UPDATE REQ' && user != data.evaluator['id'] &&
                        user == data.id) {
                        mine = true;
                        $('#pusher-maker').text(data.evaluator['name']);
                        $('#pusher-context').text(data.context + ' (your requisition)')
                    } else
                        $('#pusher-maker').text(data.evaluator['name']);

                    if (!mine) {
                        $('#pusher-context').text(data.context);
                    }
                    $('.toast').toast('show');
                }
            }
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
                <span id="pusher-maker" class="fw-bolder"></span>
                <span id="pusher-context"></span>
            </p>

        </div>
    </div>
</div>

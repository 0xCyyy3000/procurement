<!-- Receive Modal -->
<div id="receive-modal" class="copy-modal" style="display: none">
    <!-- Modal content -->
    <div class="receive-modal-content">
        <span class="close-receive-modal" id="close-receive-modal">&times;</span>
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

<div class="order-info">
    <link rel="stylesheet" href="{{ asset('css/update-order-info.css') }}">
    <div class="item receive">
        <button id="receive-btn">
            <div class="icon">
                <span class="material-icons-sharp">shopping_bag</span>
            </div>
            <div class="right">
                <h3>Receive Purchase Order</h3>
            </div>
        </button>
    </div>
    <div class="item receipt" id="attach-file">
        <div class="right">
            <button type="button">
                <span class="material-icons-sharp">attach_file</span>
                <h3>Attach Receipt</h3>
            </button>
            <input type="file" id="attach-file-window" hidden>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const receiveModal = document.getElementById("receive-modal");
        const closeReceiveModal = document.getElementById("close-receive-modal");

        closeReceiveModal.onclick = function() {
            receiveModal.style.display = "none";
        }

        $(document).on('click', '.receive', function() {
            $('#receive-modal').css('display', 'flex');
        });


    });
</script>

let token;
let description;
let priority;
let userId;

$(document).on('input', '#description', function () {
    description = $(this).val();
});

$(document).on('click', '#submit', function () {
    maker = $(this).val();
    userId = $('#userId').val();
    priority = $('#priority').val();
    token = $('#token').val();
});

$(document).on('submit', '#req-details-form', function (e) {
    e.preventDefault();
    $.ajax({
        url: '/requisition/create',
        type: 'POST',
        data: {
            _token: token,
            user_id: userId,
            maker: maker,
            priority: priority,
            description: description,
            status: 'Pending',
            approval_count: 0
        },
        dataype: 'json',
        success: function (result) {
            if (result.status == 200) {
                alert('Requisition was successfully submitted!');
                location.reload();
            }
        },
        error: function (response) {
            alert(response.responseJSON.message);
        }
    });
});
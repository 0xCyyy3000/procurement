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
        url: '/requisitions/create',
        type: 'POST',
        data: {
            _token: token,
            user_id: userId,
            maker: maker,
            priority: priority,
            description: description,
            status: 'Pending'
        },
        dataype: 'json',
        success: function (result) {
            if (result.status == 200) {
                // alert('Requisition was successfully submitted!');
            } else if (result.status == 500)
                alert('No added items, please add at least one and try again.');
        },
        error: function (response) {
            alert(response.responseJSON.message);
        }
    });
});
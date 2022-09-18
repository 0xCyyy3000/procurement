let token;
let description;
let priority;
let userId;

$(document).on('input', '#description', function () {
    description = $(this).val();
    console.log($(this).val());
});

$(document).on('click', '#submit', function () {
    userId = $(this).val();
    priority = $('#priority').val();
    // description = $('#description').text();
    token = $('#token').val();
});

$(document).on('submit', '#req-details-form', function (e) {
    e.preventDefault();
    $.ajax({
        url: '/submit/requisition',
        type: 'POST',
        data: {
            _token: token,
            user_id: userId,
            priority: priority,
            description: description,
            status: 'PENDING'
        },
        dataype: 'json',
        success: function (result) {
            console.log(result);
            if (result.status == 200) {
                alert('Requisition was successfully submitted!');
                location.reload();
            }
        }
    });
});
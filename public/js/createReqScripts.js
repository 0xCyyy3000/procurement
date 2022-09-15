const description = $('#description').text();
const prioriy = $('#priority option:selected').val();
const token = $('#token').val();
let userId;

$(document).on('click', '#submit', function () {
    userId = $(this).val();
});

$(document).on('submit', '#req-details-form', function () {
    $.ajax({
        url: '/submit/requisition',
        type: 'POST',
        data: {
            _token: token,
            user: userId,
            prioriy: prioriy,
            description: description
        },
        dataype: 'json',
        success: function (result) {

        }
    });
});
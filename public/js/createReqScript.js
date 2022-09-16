let token;
let description;
let prioriy;
let userId;

$(document).on('input', '#description', function () {
    description = $(this).val();
    console.log($(this).val());
});

$(document).on('click', '#submit', function () {
    userId = $(this).val();
    prioriy = $('#priority').val();
    // description = $('#description').text();
    token = $('#token').val();
});

$(document).on('submit', '#req-details-form', function (e) {
    e.preventDefault();
    // $.ajax({
    //     url: '/submit/requisition',
    //     type: 'POST',
    //     data: {
    //         _token: token,
    //         user: userId,
    //         prioriy: prioriy,
    //         description: description
    //     },
    //     dataype: 'json',
    //     success: function (result) {

    //     }
    // });
    console.log('PRIORITY: ' + prioriy);
    console.log('DESCRIPTION: ' + description);
    console.log('TOKEN: ' + token);
    console.log('USER ID: ' + userId);
});
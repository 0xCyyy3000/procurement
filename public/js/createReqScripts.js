let savedItems = null;
var IS_ADDING = false;
var IS_EDITING = false;
var index = null,
    items = null,
    selectedItem = null;

function indexItems() {
    $.get('/items', function (items) {

        $('#items').empty().append('<option value=""> --Select one-- </option>');
        $("#items").append('<option value="new-item"> --New item-- </option>');

        items.forEach(item => {
            $("#items").append('<option value="' + item.item_id + '">' +
                item.item + '</option>');
        });
    });
}

function itemUnits(item_id) {
    $.ajax({
        url: "{{ url('/items/" + item_id + "/units') }}",
        type: "POST",
        data: {
            item_id: item_id,
            _token: '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function (result) {
            const units = result.split(',');
            $('#units').html(
                '<option value="">-- Select unit --</option>');
            units.forEach(unit => {
                $("#units").append('<option value="' + unit + '">' +
                    unit + '</option>');
            });
        }
    });
}

// Fetching the Data from the Database in every x secs
// to achieve real time data

function liveItemsUpdate() {
    setInterval(() => {
        fetch("{{ url('/api/index') }}")
            .then((response) => {
                return response.json();
            }).then((data) => {
                items = data;
                console.log(items);
            })
            .catch((err) => {
                console.log(err)
            });
    }, 2000);
}

function liveUpdateSavedItems() {
    setInterval(() => {
        fetch("{{ url('/api/index-saved-items') }}")
            .then((response) => {
                return response.json();
            }).then((data) => {
                savedItems = data;
                console.log(savedItems);
            })
            .catch((err) => {
                console.log(err)
            });
    }, 2000);
}

$(document).ready(function () {
    // liveUpdateSavedItems();
    // liveItemsUpdate();
})

$('#items').on('change', function (e) {
    e.preventDefault();
    if ($(this).val().toUpperCase() === '') {
        $('#units').html(
            '<option value="">-- Select unit --</option>');
        $('#units').css('display', 'inline-block');
        $('#item').css('display', 'none');
        $('#unit').css('display', 'none');
        $('#items').removeClass("adding");
        IS_ADDING = false;

        $('#qty').val('');

    } else if ($(this).val().toUpperCase() === 'NEW-ITEM') {
        $('#units').css('display', 'none');
        $('#item').css('display', 'inline-block');
        $('#unit').css('display', 'inline-block');
        $('#items').toggleClass("adding");

        $('#unit').prop('required', true);
        $('#item').prop('required', true);
        IS_ADDING = true;

        $('#qty').val('');

    } else {
        $('#items').removeClass("adding");
        $('#units').css('display', 'inline-block');
        $('#item').css('display', 'none');
        $('#unit').css('display', 'none');

        $('#unit').prop('required', false);
        $('#item').prop('required', false);
        IS_ADDING = false;
    }

    if (IS_ADDING || $(this).val() === '') return;

    itemUnits($('#items option:selected').val());
    // 
});

$('#item-form').submit(function (e) {
    e.preventDefault();
    var item = $('#items option:selected').text(); // <- From <select/>
    var unit = $('#units').val();
    var url = "{{ url('/api/store-added-item') }}";

    if (IS_ADDING) {
        item = $('#item').val(); // <- From <input/>
        unit = $('#unit').val();
    }

    if (item.toUpperCase() === '' || unit.toUpperCase() === '' ||
        item.toUpperCase() === 'NEW ITEM' || item.toUpperCase() === 'NEW-ITEM') {
        alert('- Please select an Item/Unit\n- Please use a different item name');
        $('#item-form')[0].reset();
        return;
    }

    if (IS_EDITING) {
        const itemID = $('#items option:selected').val();
        url = '/api/saved-item/' + itemID;
    }

    $.ajax({
        url: url,
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            item: item,
            qty: $('#qty').val(),
            unit: unit,
            isAdding: IS_ADDING
        },
        dataType: 'json',
        success: function (result) {
            if (result.status == 200) {
                if (IS_ADDING)
                    // Adding the New Added Item to the Select Options for Items
                    $('#items').append('<option value="' + result.newItem.item_id +
                        '">' +
                        item + '</option>');

                $('#items-table').load(location.href + " #items-table");

                // Resetting the input fields to default 
                $('#item-form')[0].reset();

                $('#units').css('display', 'inline-block');
                $('#item').css('display', 'none');
                $('#item').val('');

                $('#unit').css('display', 'none');
                $('#unit').val('');

            } else if (result.edit_status == 200) {
                // Resetting the input fields to default 
                $('#item-form')[0].reset();
                IS_EDITING = false;
            }
        }
    });

});

$(document).on('click', '.edit', function () {
    IS_EDITING = true;

    // Securing the Fields
    if (IS_ADDING) {
        $('#items').removeClass("adding");
        $('#units').css('display', 'inline-block');
        $('#item').css('display', 'none');
        $('#unit').css('display', 'none');

        $('#unit').prop('required', false);
        $('#item').prop('required', false);
        IS_ADDING = false;
    }



    // // Getting the current row index of TableData from its Table parent
    // index = $(this).parent().index();

    // // Since the arrangement of SavedItems is the same from the Database,
    // // therefore indexes are correlated
    // selectedItem = savedItems[index];

    // // Assigning the fields according to its label

    // // 1) Select the Item in the Select Option
    // $('option:contains(' + selectedItem.item + ')', '#items')[0].selected = true;

    // // 2) Select the Unit in the Select Option based on what is Selected
    // // on Items Select Option. 

    // // -- Matching the Item from the Item List with the given Item
    // // to get its units
    // items.forEach(item => {
    //     if (selectedItem.item.toUpperCase() === item.item.toUpperCase()) {
    //         // Once matched, separate the values
    //         const units = item.units.split(',');

    //         // Remove existing Options
    //         $('#units option').remove();

    //         // Since everything was remove, let's add a Default Option
    //         $('#units').html(
    //             '<option value="">-- Select unit --</option>');

    //         // Then Append the Unit from Units Array
    //         units.forEach(unit => {
    //             $('#units').append('<option value="' + unit + '">' +
    //                 unit + '</option>');
    //         });

    //         // Assigning the Unit based on what is about to be Edited from the Table
    //         $('#units').val(selectedItem.unit);

    //         // Assigning the Qty
    //         $('#qty').val(selectedItem.qty);

    //         return;
    //     }
    // });

    $('#add-button').css('display', 'none');
    $('#update-button').css('display', 'flex');
});

$(document).on('click', '#update-button', function () {

});

$(document).on('click', '.remove', function () {
    // Getting the current row index of TableData from its Table parent
    index = $(this).parent().index();

    // Since the arrangement of SavedItems is the same from the Database,
    // therefore indexes are correlated
    selectedItem = savedItems[index];

});

$(document).on('submit', '#remove-item', function (e) {
    e.preventDefault();

    // Using created_at column in the Databse will be the parameter to be used 
    // when Destroying a Selected Item
    const createdAt = selectedItem.created_at;

    $.ajax({
        url: "{{ url('/api/destroy-added-item') }}",
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            createdAt: createdAt
        },
        dataType: 'json',
        success: function (result) {
            if (result.status == 200) {
                if (selectedItem.item.toUpperCase() === $('#items').val().toUpperCase())
                    // Resetting the input fields to default 
                    $('#item-form')[0].reset();

                $('#items-table').load(location.href + " #items-table");
            }
        }
    });
});
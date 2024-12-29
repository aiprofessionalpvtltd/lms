$(document).ready(function () {

// ajax call for change status of active/suspend

    $('body').on('click', '.change-status-record', function () {
        var label = $(this).data('label');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to " + label + " this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                var id = $(this).data('id');
                var status = $(this).data('status');

                $.ajax({
                    url: url,
                    type: "POST",
                    cache: false,
                    dataType: "json",
                    data: {"_token": CSRF_TOKEN, 'id': id, 'status': status},
                    success: function (data) {
                        Swal.fire({
                            title: data.success,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload()
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    $('.province').on('select2:select', function (e) {
        e.preventDefault();
        var selectedData = e.params.data;
        var provinceID = selectedData.id
        var method = 'GET';
        var type = $(this).data('type');
        $.ajax({
            type: method,
            url: getDistrictByProvince,
            data: {provinceID: provinceID},
            dataType: 'json',
            success: function (data, status, xhr) {
                const responsedata = data;
                $('#' + type + '_district').empty();
                $('#' + type + '_district').append('<option></option>');
                if (responsedata) {
                    $.each(responsedata, function (key, value) {
                        $('#' + type + '_district').append($("<option/>", {
                            value: value.id,
                            text: value.name
                        }));
                    });
                    $('#' + type + '_district').focus();

                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
            }
        });

        $.ajax({
            type: method,
            url: getCityByProvince,
            data: {provinceID: provinceID},
            dataType: 'json',
            success: function (data, status, xhr) {
                const responsedata = data;
                $('#' + type + '_city').empty();
                $('#' + type + '_city').append('<option></option>');
                if (responsedata) {
                    $.each(responsedata, function (key, value) {
                        $('#' + type + '_city').append($("<option/>", {
                            value: value.id,
                            text: value.name
                        }));
                    });
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
            }
        });
    });



    $('.vendor').on('select2:select', function (e) {
        e.preventDefault();
        var selectedData = e.params.data;
        var vendorID = selectedData.id
        var method = 'GET';
        var type = $(this).data('type');
        $.ajax({
            type: method,
            url: getVendorProductByProduct,
            data: {vendorID: vendorID},
            dataType: 'json',
            success: function (data, status, xhr) {
                const responsedata = data;
                $('#vendor_product_id').empty();
                $('#vendor_product_id').append('<option></option>');
                if (responsedata) {
                    $.each(responsedata, function (key, value) {
                        $('#vendor_product_id').append($("<option/>", {
                            value: value.id,
                            text: value.product_name
                        }));
                    });
                    $('#vendor_product_id').focus();

                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
            }
        });


    });

    $('.customer').on('select2:select', function (e) {
        e.preventDefault();
        var selectedData = e.params.data;
        var customerID = selectedData.id
        var method = 'GET';
        var type = $(this).data('type');
        $.ajax({
            type: method,
            url: getApplicationByCustomer,
            data: {customerID: customerID},
            dataType: 'json',
            success: function (data, status, xhr) {
                const responsedata = data;
                $('#application_id').empty();
                $('#application_id').append('<option></option>');
                if (responsedata) {
                    $.each(responsedata, function (key, value) {
                        $('#application_id').append($("<option/>", {
                            value: value.id,
                            text: value.application_id
                        }));
                    });
                    $('#application_id').focus();

                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
            }
        });


    });


});



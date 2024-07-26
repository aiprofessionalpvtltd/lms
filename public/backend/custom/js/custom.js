$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    if (jQuery().pickadate) {
        (function ($) {
            "use strict"

            //date picker classic default
            // https://amsul.ca/pickadate.js/date/
            $('.datepicker-default').pickadate({
                formatSubmit: 'yyyy-m-d',
                format: 'd/m/yyyy',
            });

        })(jQuery);
    }

    $('body').on('change','.custom-file-input' ,function () {
        const fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);
    })



    $(".alert").fadeTo(4000, 2000).slideUp(2000, function () {
        $(".alert").slideUp(1500);
    });

    if (jQuery().DataTable) {
        $('.button-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel', 'pdf', 'print'
            ],
            responsive: true,
            "order": [
                [0, "desc"]
            ]
        });
    }


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.country').on('select2:select', function (e) {
        e.preventDefault();
        var selectedData = e.params.data;
        var countryID = selectedData.id
        var method = 'GET';
        var type = $(this).data('type');
        console.log(type)
        $.ajax({
            type: method,
            url: getProvinceByCountry,
            data: {countryID: countryID},
            dataType: 'json',
            success: function (data, status, xhr) {
                const responsedata = data;
                $('#' + type + '_province').empty();
                $('#' + type + '_province').append('<option></option>');
                if (responsedata) {
                    $.each(responsedata, function (key, value) {
                        $('#' + type + '_province').append($("<option/>", {
                            value: value.id,
                            text: value.name
                        }));
                    });
                    $('#' + type + '_province').focus();
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
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


    // ajax call for change status of active/suspend
    $('.change-status-record').on('click', function () {

        var label = $(this).data('label');

        Swal.fire({

            title: 'Are you sure?',

            text: "You want to " + label + " this!",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');

                var id = $(this).data('id');
                var email = $(this).data('email');

                var status = $(this).data('status');

                $.ajax({
                    type: "POST",

                    url: url,

                    data: {"_token": CSRF_TOKEN, 'id': id, 'status': status, 'email': email},

                    dataType: "json",

                    cache: false,

                    success: function (data, status, xhr) {

                        console.log(status, xhr)
                        Swal.fire({

                            title: data.success,

                            timer: 2000,

                            showConfirmButton: false,

                            onClose: () => {

                                location.reload();

                            }

                        });
                    }

                });

            }
        });
    })


    // ajax call for change status of active/suspend
    $('body').on('click', '.delete-record', function (e) {
        e.preventDefault();
        Swal.fire({

            title: 'Are you sure?',

            text: "You want to delete this record!",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');

                var id = $(this).data('id');


                $.ajax({
                    type: "POST",

                    url: url,

                    data: {"_token": CSRF_TOKEN, 'id': id},

                    dataType: "json",

                    cache: false,

                    success: function (data, status, xhr) {

                        Swal.fire({

                            title: data.success,

                            timer: 2000,

                            showConfirmButton: false,

                            onClose: () => {

                                location.reload();

                            }

                        });
                    }

                });

            }
        });
    })

    // ajax call for change status of active/suspend
    $('.apply-for-position').on('click', function () {

        var roleName = $(this).data('role-name');

        Swal.fire({

            title: 'Are you sure?',

            text: "You want to apply for role of " + roleName + " ?",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {


                var url = $(this).data('url');
                var demand = $(this).data('demand');
                var role = $(this).data('role');

                $.ajax({
                    type: "POST",

                    url: url,

                    data: {"_token": CSRF_TOKEN, 'demand': demand, 'role': role},

                    dataType: "json",

                    cache: false,

                    success: function (data, status, xhr) {


                        if (data.status == true) {
                            Swal.fire({
                                title: 'Good job!',

                                text: data.success,

                                // timer: 3000,

                                showConfirmButton: true,


                                onClose: () => {

                                    location.reload();

                                }

                            });
                        } else {
                            Swal.fire({
                                title: 'Sorry!',

                                text: data.error,

                                // timer: 3000,

                                type: 'warning',

                                showConfirmButton: true,

                                onClose: () => {

                                    location.reload();

                                }

                            });
                        }

                    }

                });

            }
        });
    })

    // ajax call for  retake test
    $('body').on('click', '.retake-test', function (e) {
        e.preventDefault();
        Swal.fire({

            title: 'Are you sure?',

            text: "You want to retake analytical test from candidate !",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');

                var id = $(this).data('id');


                $.ajax({
                    type: "POST",

                    url: url,

                    data: {"_token": CSRF_TOKEN, 'id': id},

                    dataType: "json",

                    cache: false,

                    success: function (data, status, xhr) {

                        Swal.fire({

                            title: data.success,

                            timer: 2000,

                            showConfirmButton: false,

                            onClose: () => {

                                location.reload();

                            }

                        });
                    }

                });

            }
        });
    })

    // ajax call for  retake test
    $('body').on('click', '.delete-application', function (e) {
        e.preventDefault();
        Swal.fire({

            title: 'Are you sure?',

            text: "You want to delete application of candidate !",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');

                var id = $(this).data('id');


                $.ajax({
                    type: "POST",

                    url: url,

                    data: {"_token": CSRF_TOKEN, 'id': id},

                    dataType: "json",

                    cache: false,

                    success: function (data, status, xhr) {

                        Swal.fire({

                            title: data.success,

                            timer: 2000,

                            showConfirmButton: false,

                            onClose: () => {

                                location.reload();

                            }

                        });
                    }

                });

            }
        });
    })




    // step form wizard for assessment test

    if (!$().steps) {
        console.warn('Warning - steps.min.js is not loaded.');
        return;
    }

    // Stop function if validation is missing
    if (!$().validate) {
        console.warn('Warning - validate.min.js is not loaded.');
        return;
    }

});



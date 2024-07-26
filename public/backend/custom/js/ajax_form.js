$(document).ready(function () {

    $(document).on('click', '.ajax-form-btn', function (e) {
        e.preventDefault();

        var form = $(this).parent().parent('.ajax-form');

        const method = form.attr('method');

        const url = form.data('action');

        const formData = form.serialize();

        $.ajax({

            url: url,

            type: method,

            cache: false,

            dataType: "json",

            data: formData,

            success: function (response) {

                Swal.fire({

                    title: response.success,

                    timer: 2000,

                    showConfirmButton: false,

                    onClose: () => {

                        $('.modal').modal('hide');

                        location.reload();
                    }

                });

            },
            error: function (err) {


                $.each(err.responseJSON.errors, function (key, value) {

                    var errorPlacement = $("input[name='" + key + "']");
                    errorPlacement.next().html(value[0]);
                    errorPlacement.next().removeClass('d-none');

                    var errorSelectPlacement = $("select[name='" + key + "']");
                    errorSelectPlacement.next().html(value[0]);
                    errorSelectPlacement.next().removeClass('d-none');

                });
            },

        });
    })

    $('#addBtn').on('click', function (e) {
        e.preventDefault();
        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxAddForm').data('action', action);
        $('#' + modalID).modal('show');

    });

    $(document).on('click', '#editBtn', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxEditForm').data('action', action);
        $('#' + modalID).modal('show');


        var url = $(this).data('url');
        var id = $(this).data('id');


        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {


                $.each(data.responseData, function (key, value) {

                    if (key === 'is_possession' && value == 1) {
                        $('#possession_edit').prop('checked', true)
                    } else if (key === 'is_transfer' && value == 1) {
                        $('#transfer_edit').prop('checked', true)
                    }
                    var inputElement = $("input[name='" + key + "']");

                    if (inputElement.attr('type') != 'checkbox'){
                        var errorPlacement = $("input[name='" + key + "']");
                        errorPlacement.val(value);
                    }



                });

            }

        });


    })

    $('#addDistrictBtn').on('click', function (e) {
        e.preventDefault();
        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxAddDistrictForm').data('action', action);
        $('#' + modalID).modal('show');

    });

    $(document).on('click', '#editDistrictBtn', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxEditDistrictForm').data('action', action);
        $('#' + modalID).modal('show');


        var url = $(this).data('url');
        var id = $(this).data('id');
        var province = $(this).data('province');

        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {

                $.each(data.responseData, function (key, value) {
                    var districtName = $("input[name='" + key + "']");
                    var provinceName = $("select[name='" + key + "']");
                    districtName.val(value);
                    provinceName.val(value)

                });

            }

        });


    })


    $('#addPlotChargesBtn').on('click', function (e) {
        e.preventDefault();
        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxAddPlotChargesForm').data('action', action);
        $('#' + modalID).modal('show');

    });

    $(document).on('click', '#editPlotChargesBtn', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');
        var action = $(this).data('action');
        $('#AjaxEditPlotChargesForm').data('action', action);
        $('#' + modalID).modal('show');


        var url = $(this).data('url');
        var id = $(this).data('id');

        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {

                console.log(data)

                $.each(data.responseData, function (key, value) {
                    var districtName = $("input[name='" + key + "']");
                    var provinceName = $("select[name='" + key + "']");
                    districtName.val(value);
                    provinceName.val(value)

                });

            }

        });


    })


// ajax call for change status of active/suspend
    $('body').on('click', '.change-status-record', function () {

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


    $(document).on('click', '#viewModalBtn', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');

        var selectedModal = $('#' + modalID).modal('show');

        var url = $(this).data('url');

        var id = $(this).data('id');


        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {

                $.each(data.responseData, function (key, value) {
                    var elementPlacement = $("#" + key);
                    elementPlacement.html(value);

                });

                var educations = data.educationsArray;
                $('#educationDiv').empty();
                jQuery.each(educations, function (i, val) {
                    var attchements = data.attachment[val.id];

                    var html = ' <div class="row mt-4">' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Highest Degree<span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="education_level_id">' + val.education_level_id + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Degree Title<span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="degree_title">' + val.degree_title + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Institution<span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="institution">' + val.institution + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  "> Degree Duration <span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                            <p id="degree_duration">' + val.degree_duration + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Degree Start Date <span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="degree_start_date">' + val.degree_start_date + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Degree Completion Date <span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="degree_completion_date">' + val.degree_completion_date + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">Result Declaration Date <span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="result_declaration_date">' + val.result_declaration_date + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">GPA / Obtain Marks<span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="gpa">' + val.gpa + '</p>' +
                        '                                    </div>' +
                        '                                    <div class="col-md-4">' +
                        '                                        <label class="col-form-label font-weight-bold  ">CGPA / Total Marks<span' +
                        '                                                class="text-danger">*</span> </label>' +
                        '                                        <p id="total_marks">' + val.total_marks + '</p>' +
                        '                                    </div>' +
                        '                                </div>';
                    var images_div = '';
                    jQuery.each(attchements, function (ii, attch) {
                        if (attch.extension == 'pdf') {
                            var viewpdf = '';
                            var viewimage = 'd-none';

                        } else {
                            var viewpdf = 'd-none';

                            viewimage = '';
                        }
                        images_div += ' <div class="row mt-3"> ' +
                            '<div class="col-md-4">' +
                            '<p>' + attch.name + '</p>' +
                            '</div>' +
                            '<div class="col-md-6">' +
                            '<a class=" ' + viewpdf + '" target="_blank" href="' + attchements[ii].attachment_url + '">' +
                            '<img class="img-fluid  ' + viewpdf + '" src="' + pdfIcon + '" width="100" height="80" alt="' + attch.name + '">' +
                            '</a>' +
                            '<a class=" ' + viewimage + '"  target="_blank" href="' + attchements[ii].attachment_url + '">' +
                            '<img class="img-fluid ' + viewimage + '" src="' + attchements[ii].attachment_url + '" width="100" height="80" alt="' + attch.name + '">' +
                            '</a>' +
                            '</div>';
                    });

                    html += images_div;
                    html += '</div>';


                    $('#educationDiv').append(html);
                });


            }

        });


    })

    $(document).on('click', '#viewMentorBtn', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');

        $('#' + modalID).modal('show');

        var url = $(this).data('url');

        var id = $(this).data('id');


        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {

                $.each(data.responseData, function (key, value) {
                    var elementPlacement = $("#" + key + "");
                    elementPlacement.html(value);

                });


            }

        });


    })


    // AJAX requests
    $('#sweetalert_form').on('click', function () {
        var title = $(this).data('title');
        var url = $(this).data('url');
        var method = 'POST';

        Swal.fire({
            title: 'Add ' + title,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            inputPlaceholder: 'Enter Name Of ' + title,
            inputClass: 'form-control',
            showCancelButton: true,
            confirmButtonText: 'Look up',
            showLoaderOnConfirm: true,
            inputValidator: function (value) {
                return !value && 'You need to write something!'
            },
            allowOutsideClick: false
        }).then(function (result) {

            if (result.value) {
                var name = result.value;

                $.ajax({

                    url: url,

                    type: method,

                    cache: false,

                    dataType: "json",

                    data: {"name": name, "_token": CSRF_TOKEN,},

                    success: function (response) {

                        Swal.fire({

                            title: response.success,

                            timer: 2000,

                            showConfirmButton: false,

                            onClose: () => {

                                location.reload();
                            }

                        });

                    },
                    error: function (err) {

                        swalInit.showValidationMessage(
                            'Request failed: ' + err.responseJSON.errors
                        );

                    },

                });
            }
        });
    });

    $('.quiz-option').on('click', function () {

        var attemptQuizID = $(this).val();
        var optionID = $(this).data('option');
        var questionID = $(this).data('question');
        var quizID = $(this).data('quiz');
        var internID = $(this).data('intern');
        var url = $(this).data('url');
        $.ajax({

            url: url,

            type: "POST",

            cache: false,

            dataType: "json",

            data: {
                "_token": CSRF_TOKEN,
                'attemptQuizID': attemptQuizID,
                'optionID': optionID,
                'questionID': questionID,
                'quizID': quizID,
                'internID': internID,
            },

            success: function (data) {


            }

        });

    })


    // add option modal
    $(document).on('click', '.add-option', function (e) {
        e.preventDefault();
        var modalID = $(this).data('target');
        var action = $(this).data('url');
        var questionID = $(this).data('id');
        $('#question_id').val(questionID);
        $('#addOptionForm').data('action', action);
        $('#' + modalID).modal('show');
    })

    $(document).on('click', '.add-option-form-btn', function (e) {
        e.preventDefault();

        var form = $(this).parent().parent('.add-option-form');

        const method = form.attr('method');

        const url = form.data('action');

        const formData = form.serialize();

        // return false;
        $.ajax({

            url: url,

            type: method,

            cache: false,

            dataType: "json",

            data: formData,

            success: function (response) {

                Swal.fire({

                    title: response.message,

                    timer: 3000,

                    showConfirmButton: false,

                    onClose: () => {

                        $('.modal').modal('hide');

                        location.reload();
                    }

                });

            },
            error: function (err) {

                $.each(err.responseJSON.errors, function (key, value) {

                    var errorPlacement = $("input[name='" + key + "']");
                    errorPlacement.next().html(value[0]);
                    errorPlacement.next().removeClass('d-none');

                });
            },

        });
    })

    $(document).on('click', '.view-option', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');

        $('#' + modalID).modal('show');

        var url = $(this).data('url');

        var id = $(this).data('id');

        var QuestionName = $(this).data('name');


        $('#question').html(QuestionName);

        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {

                $('#optionDiv').empty();

                $.each(data.responseData, function (key, value) {
                    var html = '<div class="col-md-3">' +
                        '<label class="col-form-label font-weight-bold">' + value.option + '</label>' +
                        '<p>' + value.is_correct + '</p>' +
                        '</div>';
                    $('#optionDiv').append(html);
                });


            }

        });


    })

    $(document).on('click', '.edit-option', function (e) {
        e.preventDefault();

        var modalID = $(this).data('target');

        $('#' + modalID).modal('show');

        var url = $(this).data('url');

        var id = $(this).data('id');

        var QuestionName = $(this).data('name');


        $.ajax({

            url: url,

            type: "GET",

            cache: false,

            dataType: "json",

            data: {"_token": CSRF_TOKEN, 'id': id},

            success: function (data) {
                $('#optionEditDiv').empty();

                $.each(data.responseData, function (key, value) {

                    var html = ' <div class="row">  <div class="col-md-6">' +
                        '                        <label class="col-form-label  ">Option Title <span' +
                        '                                class="text-danger">*</span> </label>' +
                        '                        <div' +
                        '                            class="form-group form-group-feedback form-group-feedback-right">' +
                        '                            <input type="text" name="name[' + value.id + ']" required' +
                        '                                   class="form-control"' +
                        '                                   value="' + value.option + '"' +
                        '                                   placeholder="Your Option Title">' +
                        '                            <span class="error text-danger d-none"></span>' +
                        '                        </div>' +
                        '                    </div>' +
                        '                    <div class="col-md-6">' +
                        '                        <label class="col-form-label  ">Is Correct? <span class="text-danger">*</span>' +
                        '                        </label>' +
                        '                        <div class="form-group mb-3 mb-md-2">' +
                        '                            <div class="custom-control custom-control-right custom-radio custom-control-inline">' +
                        '<div class="form-check form-check-inline form-check-right">' +
                        '<label class="form-check-label">' +
                        'Yes' +
                        '<input type="radio" class="form-check-input" id="is_correct_' + value.id + '"   name="is_correct[' + value.id + ']" value="1">' +
                        '</label>' +
                        '</div>' +
                        '<div class="form-check form-check-inline form-check-right">' +
                        '<label class="form-check-label">' +
                        'No' +
                        '<input type="radio" class="form-check-input" id="is_not_correct_' + value.id + '"  name="is_correct[' + value.id + ']"  value="0">' +
                        '</label>' +
                        '</div>' +
                        '                            <span class="error text-danger d-none"></span>' +
                        '                        </div>' +
                        '                  </div>  </div>';

                    $('#optionEditDiv').append(html);

                    $('#editQuestion').html(QuestionName);
                    $('#edit_question_id').val(id);

                    // check the radio button yes no
                    if (value.is_correct == 1) {
                        $('#is_correct_' + value.id).prop('checked', true);

                    } else {
                        $('#is_not_correct_' + value.id).prop('checked', true);

                    }

                });


            }

        });


    })

    $(document).on('click', '.add-option-edit-form-btn', function (e) {
        e.preventDefault();

        var form = $(this).parent().parent('.add-option-edit-form');

        const method = form.attr('method');

        const url = form.data('action');

        const formData = form.serialize();


        $.ajax({

            url: url,

            type: method,

            cache: false,

            dataType: "json",

            data: formData,

            success: function (response) {

                Swal.fire({

                    title: response.message,

                    timer: 3000,

                    showConfirmButton: false,

                    onClose: () => {

                        $('.modal').modal('hide');

                        location.reload();
                    }

                });

            },
            error: function (err) {

                $.each(err.responseJSON.errors, function (key, value) {

                    var errorPlacement = $("input[name='" + key + "']");
                    errorPlacement.next().html(value[0]);
                    errorPlacement.next().removeClass('d-none');

                });
            },

        });
    })

    $(document).on('click', '.send-offer', function () {

        var label = $(this).data('label');

        Swal.fire({

            title: 'Are you sure?',

            text: "You want to send offer letter on email!",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');


                $.ajax({

                    url: url,

                    type: "POST",

                    cache: false,

                    dataType: "json",

                    data: {"_token": CSRF_TOKEN},

                    success: function (data) {

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


    $(document).on('click', '.apply-now', function () {

        var label = $(this).data('label');

        Swal.fire({

            title: 'Are you sure?',

            text: "You want to send apply now email to candidate!",

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: "Yes",

            cancelButtonText: "No"

        }).then((result) => {

            if (result.isConfirmed) {

                var url = $(this).data('url');


                $.ajax({

                    url: url,

                    type: "POST",

                    cache: false,

                    dataType: "json",

                    data: {"_token": CSRF_TOKEN},

                    success: function (data) {

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


});



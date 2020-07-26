function popup(type, title = null, html = null) {
    Swal.fire({
        icon: type,
        title: title,
        html: html
    });
}

function customAlert(type, message) {
    return '<div class="alert alert-'+ type +' alert-dismissible fade show" role="alert">\n' +
            message +
        '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
        '    <span aria-hidden="true">&times;</span>\n' +
        '  </button>\n' +
        '</div>';
}

$.validator.setDefaults({
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    }
});

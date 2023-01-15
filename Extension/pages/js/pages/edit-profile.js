var auth_user = { id: null, name: '', email: '' };

$(document).ready(function () {
    LS.getItem('auth_user', function(userData) {
        auth_user = userData;

        $("#name").val(auth_user.name);
        $("#email").val(auth_user.email);
    });

    $("#saveChanges").on('click', function () {
        var name = $("#name");
        var password = $("#password");
        var newPassword = $("#newPassword");
        var confirmPassword = $("#confirmPassword");

        if (isNotEmpty(name) || (isNotEmpty(password) && isNotEmpty(newPassword) && newPassword.val() === confirmPassword.val())) {
            var postData = {
                updateProfile: '1',
                name: name.val(),
                password: password.val(),
                email: $("#email").val(),
                newPassword: newPassword.val()
            }

            $.post('', 'POST', postData, function (r) {
                r = JSON.parse(r);

                if (r.success) {
                    auth_user.name = name.val();
                    localStorage.setItem('auth_user', JSON.stringify(auth_user));
                    $("#response").removeClass('text-danger').addClass('text-success').html('Profile has been updated.','edit_profile');
                } else
                    $("#response").addClass('text-danger').removeClass('text-success').html(r.reason);
            });
        }
    });
});

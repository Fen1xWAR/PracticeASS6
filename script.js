
function Notification(text, duration = 2000, root='.toast-container') {
    return {
        obj: $('<div class="toast  text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true"></div>'),
        text: text,
        duration: duration,
        root: root,
        show: function () {
            const toast = this.obj;
            const toastBody = $('<div class="toast-body"></div>');
            if($('.toast').length >= 3){
                $(this.root).empty();
            }
            toastBody.text(this.text);
            toast.append(toastBody);
            const toastContainer = $(this.root);
            toastContainer.append(toast);
            toast.toast({ delay: this.duration });
            toast.toast('show');
            setTimeout(() => {
                toast.toast('hide');
            }, this.duration);
        },
        hide: function () {
            this.obj.remove();
        }
    }
}
function ajaxErrorHandling(xhr) {
    console.log(xhr);
    console.error(xhr.responseText)
    const errorMessage = JSON.parse(xhr.responseText).message;
    Notification(errorMessage).show();

}

function openEditModal(jsonStr) {

    document.getElementById('userId').value = jsonStr['user_id'];
    document.getElementById('userEmail').value = jsonStr['email'];
    document.getElementById('userName').value = jsonStr['name'];
    document.getElementById('userSurname').value = jsonStr['surname'];
    document.getElementById('userLastname').value = jsonStr['lastname'];
    document.getElementById('userRole').value = jsonStr['role_id'];

    $('#editUserModal').modal('show');
}
function setDefaultPassword() {
    $('#userPassword').val( `${$('#userSurname').val()}${$('#userName').val()[0]}${$('#userLastname').val()[0]}`)
}

function removeUser() {
    let userId = $('#userId').val()
    $.ajax({
        type: "post",
        url: "Services/usersService.php",
        data: {"removeUser": userId},
        success: function (response) {
            setTimeout(() => {
                location.reload()
            }, 800)

        },
        error: ajaxErrorHandling


    });
}
function saveUserChanges() {
    // Получаем данные из формы
    var formData = {
        userId: $('#userId').val(),
        userPassword : $('#userPassword').val(),
        userEmail: $('#userEmail').val(),
        userName: $('#userName').val(),
        userSurname: $('#userSurname').val(),
        userLastname: $('#userLastname').val(),
        userRole: $('#userRole').val(),
    };
    console.log(formData)

    $.ajax({
        type: "post",
        url: "Services/usersService.php",
        data: {"editUser": formData},
        success: function (response) {
            setTimeout(() => {
                location.reload()
            }, 800)

        },
        error: ajaxErrorHandling


    });

    // Закрываем модальное окно
    $('#editUserModal').modal('hide');
}

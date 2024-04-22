
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

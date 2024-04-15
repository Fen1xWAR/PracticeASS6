function Notification(Text,Duration=2000,Root ='.wrapper'){
    return {
        obj: $('<div class="AlertPosition"></div>'),
        text: Text,
        duration: Duration,
        root: Root,
        show: function () {
            $(".AlertPosition").remove()
            $(this.root).append(this.obj)
            this.obj.append('<div class="AlertBox"></div>')
            this.obj.children().text(this.text)
            setTimeout((context) => {
                context.hide()
            }, this.duration, this)
        },
        hide: function () {
            this.obj.empty()
            this.obj.remove()

        }

    }
}
const uiInterface = {
    reaction: {
        loadingEl: $("#loading"),
        toastEl: $(".reaction"),
        toast: new bootstrap.Toast(document.querySelector(".reaction")),
        loading: false,
        error: {
            title: null,
            align: 'bottom-0 end-0',
            color: 'text-bg-danger',
            serverSide: null,
            autoHide: true,
        },
        success: {
            title: "Əməliyyat uğurla yerinə yetirildi",
            align: 'top-0 start-50 translate-middle-x',
            color: 'text-bg-success',
            autoHide: true
        },
        info: {
            title: null,
            align: 'top-50 start-50 translate-middle-x',
            color: 'text-bg-primary',
            autoHide: true
        },
        generate(active) {
            this.toastEl.parent().addClass(active.align)
            this.toastEl.addClass(active.color)
            this.toastEl.find("#toast-message").html(active.title)
            return this
        },
        show() {
            this.toast.show()
        },
    },

    get error() {
        return this.reaction.error
    },

    set error(title) {
        this.error.title = (this.error.serverSide ? 'Serverə göndərilən zaman ' : 'İcra zamanı ') + 'xəta baş verdi: ' + title
        this.reaction.generate(this.error).show()
        throw new Error(this.error.title)
    },

    get success() {
        return this.reaction.success
    },

    set success(title) {
        if(typeof title === 'string' || title instanceof String) {
            this.success.title = title
        }

        this.reaction.generate(this.success).show()
    },

    get info() {
        return this.reaction.info
    },

    set info(title) {
        this.info.title = title
        this.reaction.generate(this.info).show()
    },
    
    set loading(bool) {
        this.reaction.loading = bool
        bool ? this.reaction.loadingEl.removeClass("d-none") : this.reaction.loadingEl.addClass("d-none")
    }
}
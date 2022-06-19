import Vue from "vue"
Vue.prototype.$swal = {
    confirm(title, text, theme, callback, opt = {}) {
        let option = Object.assign({
            title: title,
            html: text,
            type: theme,
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            reverseButtons: true,
            showCloseButton: false,
        }, opt)
        swal(option).then((result) => {
            if (result.value) {
                return callback(result)
            }
        })
    },
    raw_confirm(title, text, theme, callback, opt = {}) {
        let option = Object.assign({
            title: title,
            html: text,
            type: theme,
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            reverseButtons: true,
            showCloseButton: false,
        }, opt)
        swal(option).then((result) => {
            return callback(result)
        })
    },
    input(title, text, theme, callback, opt = {}) {
        if (!opt.type)
            opt.type = "text"
        if (!opt.placeholder)
            opt.placeholder = ""
        if (!opt.inputValue)
            opt.inputValue = ""
        swal({
            title: title,
            text: text,
            type: theme,
            inputValue: opt.inputValue,
            inputPlaceholder: opt.placeholder,
            input: opt.type,
            showCancelButton: true,
            reverseButtons: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Continuar',
            showCloseButton: false,
            showLoaderOnConfirm: true,
            preConfirm: (result) => {
                callback(result)
            }
        })
    },
    simple(text) {
        swal(text)
    },
    vue(target, from, options = {}) {
        let data = {
            html: `<div id="vue-modal-content"><div>`,
            customClass: "swal-wide",
            showCancelButton: false,
            showConfirmButton: false,
            onClose() {
                $(target).appendTo(from)
            }
        }
        data = Object.assign(data, options)
        let res = swal(data)
        from.children().appendTo("#vue-modal-content")
        return res
    }
}

let uuid = 0
Vue.mixin({
    beforeCreate() {
        this.uuid = uuid.toString()
        window.uuid = this.uuid
        uuid += 1
    },
    methods: {
        nextUuid() {
            return uuid.toString()
        },
        array_chunk(arr, n) {
            var R = []
            for (var i = 0, len = arr.length;i < len;i += chunkSize)
                R.push(arr.slice(i, i + chunkSize))
            return R
        }
    }
})
require('./app/init')
require('./libs/init')

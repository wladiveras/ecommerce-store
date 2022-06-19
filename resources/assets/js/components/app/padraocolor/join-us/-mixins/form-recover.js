function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function storageAvailable() {
    var t = 't'
    try {
        localStorage.setItem(t, t)
        localStorage.removeItem(t)
        return 1
    } catch (e) {
        return 0
    }
}

function getId() {
    if (storageAvailable()) {
        return localStorage.getItem('recoverId') || makeId()
    }
}

function makeId() {
    let id = getCookie('_gid')

    if (!id) {
        id = String(Math.random()).replace('.', '')
    }
    return id + `${(+new Date)}`
}

function saveIdOnStorage(id) {
    if (storageAvailable()) {
        localStorage.setItem('recoverId', id)
    }
}

export default {
    data() {
        return {
            recoverId: null
        }
    },
    methods: {
        startRecover() {
            //let id = getId()

            //saveIdOnStorage(id)
            //this.recoverId = id
        },
        sendRecover(form) {
            //this.$http.post('/seja-um-revendedor/progress', {
            //    ...form,
            //    id: this.recoverId
            //})
        },
        saveRecover() {
            this.sendRecover(this.form)
        }
    }
}

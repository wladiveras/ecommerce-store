require('./bootstrap')
require('./validator')
require('./axios')
require("./Object")
global.swal = require('sweetalert2')
global.StackBlur = require('stackblur-canvas')

String.prototype.trunc = function (n, useWordBoundary) {
    if (this.length <= n) { return this }
    var subString = this.substr(0, n - 1)
    return (useWordBoundary ? subString.substr(0, subString.lastIndexOf(' ')) : subString) + "..."
}

String.prototype.currency = function () {
    let decimal = Number(this).toFixed(2)
    return Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(decimal)
}
Number.prototype.currency = function () {
    return this.toString().currency()
}

String.prototype.capitalize = function () {
    return this.replace(/(?:^|\s)\S/g, function (a) { return a.toUpperCase() })
}

String.prototype.slug = function () {
    let str = this
    str = str.replace(/^\s+|\s+$/g, '') // trim
    str = str.toLowerCase()

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;"
    var to = "aaaaeeeeiiiioooouuuunc------"
    for (var i = 0, l = from.length;i < l;i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-') // collapse dashes

    return str
}
const mix = require("laravel-mix")
const launchMiddleware = require("launch-editor-middleware")

/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
*/
//mix.browserSync('dev.rumar');

mix.options({
    processCssUrls: false
})
mix.autoload({
    jquery: ["jQuery", "$"]
})

mix.js("resources/assets/js/app.js", "public/js")
    .sass("resources/assets/sass/app.scss", "public/css")
    .extract([
        "axios",
        "laravel-echo",
        "element-ui",
        "pusher-js",
        "vue",
        "stackblur",
        "stackblur-canvas",
        "jquery",
        "bootstrap",
        "chosen-js",
        "datatables.net/js/jquery.dataTables.js",
        "datatables.net-bs4/js/dataTables.bootstrap4.js",
        "popper.js",
        "toastr",
        "vue2-dropzone",
        "vue-toastr-2",
        "vue-the-mask",
        "v-money",
        "lodash",
        "vue-js-modal",
        "bootstrap-vue",
        "vue-toasted",
        // "bs4-summernote",
        "summernote/src/js/summernote.js",
        "vue-introjs"
    ])
if (process.env.MIX_BROWSERSYNC_URL) {
    mix.browserSync({
        proxy: process.env.MIX_BROWSERSYNC_URL,
        middleware: [
            {
                route: "/__open-in-editor",
                handle: launchMiddleware("code")
            }
        ]
    })
}
if (mix.inProduction()) {
    mix.version()
}


mix.babelConfig({
    plugins: [
        "@babel/plugin-syntax-dynamic-import"
    ]
})

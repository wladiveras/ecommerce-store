<template>
    <div v-loading="loading">
	    <textarea ref="textarea" style="display:none;" v-model="text" :name="name"></textarea>
    </div>
</template>
<script>
export default {
    props: {
        disabled :
        {
            type: Boolean,
            default: false
        },
        disableresize:
        {
            type: Boolean,
            default: false
        },
        name:
        {
            type: String,
            default: 'text'
        },
        routeuploadimage:
        {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        height: {
            type: Number,
            default: 350
        },
        minHeight: {
            type: Number,
            default: 200
        },
        maxHeight: {
            type: Number,
            default: 700
        },
        focus: {
            type: Boolean,
            default: true
        },
        toolbar :{
            type: Array,
            default: () =>  [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['insert', ['link', 'picture']],
                ['misc', ['codeview','fullscreen']],
                ['height', ['height']]
            ]
        }
    },
    data() {
		return {
			text : null,
            initialized : false,
            loading : false
		}
	},
    mounted: function () {
        var initOptions = {
            lang : "pt-BR",
            placeholder: this.placeholder,
            popover: {},
            toolbar: this.toolbar,
            height: this.height,
            disableResizeEditor: this.disableresize,
            minHeight: this.minHeight,
            maxHeight: this.maxHeight,
            focus: this.focus,
            callbacks: {
                onInit: () => {
                    this.$emit('onInit')
                },
                onEnter: () => {
                    this.$emit('onEnter')
                },
                onFocus: () => {
                    this.$emit('onFocus')
                },
                onBlur: () => {
                    this.$emit('onBlur')
                },
                onKeyup: (e) => {
                    this.$emit('onKeyup', e)
                },
                onKeydown: (e) => {
                    this.$emit('onKeydown', e)
                },
                onPaste: (e) => {
                    this.$emit('onPaste', e)
                },
                onImageUpload: (files) => {
                    this.$emit('onImageUpload', files)
                },
                onChange: (contents) => {
                    this.$emit('onChange', contents)
                    this.$emit('input', contents)
                },
                onImageUpload: (image) => {
                    this.loading = true
                    this.uploadImage(image[0]);
                }
            }
        }
        var params = Object.assign({}, initOptions);
        $(this.$refs.textarea).summernote(params)
        if (this.disabled) {
            $(this.$refs.textarea).summernote('disable');
        }
        $(".note-group-image-url").remove();
        this.run('code',this.$attrs.value)
    },
    beforeDestroy: function () {
        $(this.$refs.textarea).summernote('destroy')
    },
    methods: {
        uploadImage:function(image)
        {
            let data = new FormData();
            data.append("file", image);
            let self = this;
            $.ajax({
                url: self.routeuploadimage,
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: "POST",
                success: function(response) 
                {
                    self.loading = false
                    if(["bmp","jpeg","jpg","gif","bmp","png"].includes(response.data.extension)) {
                        var image = $('<img>').attr('src', response.data.raw_url);
                        $(self.$refs.textarea).summernote("insertNode", image[0]);
                    } else {
                        var image = $('<a>').attr('href', response.data.raw_url).attr('target', '_BLANK').html(response.data.raw_url).attr("class","link");
                        $(self.$refs.textarea).summernote("insertNode", image[0]); 
                    }
                },
                error: function(data) {
                    console.log(data);
                    self.loading = false
                }
            });
        },
        run: function (code, value) {
            if (typeof value === undefined) {
                return $(this.$refs.textarea).summernote(code)
            } else {
                return $(this.$refs.textarea).summernote(code, value)
            }
        }
    }
}
</script>

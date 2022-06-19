<style>
.custom-dropzone-parent .dz-clickable {
    border: 1px dashed #c7c7c7;
    border-radius: 5px;
}

.custom-dropzone-parent .text-custom {
    color: #909090;
}

.custom-dropzone-parent .text-custom .green {
    color: #1b4e01;
}

.dropzone {
    text-align: center;
}

.dz-remove {
    display: inline-block !important;
    position: absolute;
    top: -12px !important;
    bottom: unset !important;
    right: -12px !important;
    border-radius: 100% !important;
    width: 30px !important;
    height: 30px !important;
    color: #44701c !important;
    background: #d6ef63 !important;
    border: 1px solid #44701c !important;
    font-size: 20px !important;
    text-decoration: none !important;
}

.dz-remove .text {
    position: absolute !important;
    top: -1px !important;
    left: 3px !important;
    right: 0px !important;
}
</style>
<template>
    <div class="row">
        <div class="col-sm-12 custom-dropzone-parent">
            <vue-dropzone
                ref="myVueDropzone"
                id="dropzone"
                :options="dropzoneOptions"
                v-on:vdropzone-max-files-exceeded="maxReached"
                v-on:vdropzone-removed-file="removed"
                v-on:vdropzone-complete="complete"
                v-on:vdropzone-success="uploaded"
            ></vue-dropzone>
        </div>
    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
export default {
    props: ["shared"],
    data() {
        return {
            upload: [],
            dropzoneOptions: {
                url: "configuracao_produto/upload",
                addRemoveLinks: true,
                thumbnailWidth: 250,
                dictRemoveFile: "",
                dictCancelUpload: "",
                maxFiles: 2,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.cdr,.psd,.pdf",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                dictDefaultMessage: '<div class="text-center">' +
                    '   <p class="mb-2"><img src="/svg/upload/cloud-upload.svg"></p>' +
                    '   <p><span class="text-custom">Arraste o arquivo aqui ou <span class="green">clique para fazer o upload</a></span></p>' +
                    '</div>'
            }
        }
    },
    watch:
    {
        upload(val) {
            this.shared.data.art_creation_info.company_info["upload"] = val
        }
    },
    components: {
        vueDropzone: vue2Dropzone
    },
    methods: {
        complete() {
            $(".dz-remove").html("<span class='text'>×<span>")
        },
        uploaded(file, response) {
            this.upload.push(response.data)

        },
        maxReached(file) {
            this.$refs.myVueDropzone.removeFile(file)
            return this.$toastr.info("<b>Ooops,</b> Apenas 2 imagem por upload")
        },
        removed(file) {
            this.upload = null
        }

    }
}
</script>

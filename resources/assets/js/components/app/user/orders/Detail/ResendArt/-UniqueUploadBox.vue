<template>
<div v-if="!disable">
    <p class="row mb-1 justify-content-center text-center" >
        <b v-if="!content.file">{{label}}</b>
        <span v-else><a href="#" class="delete-link" @click.prevent="deleteFile()">Excluir {{label}}</a></span>
    </p>
    <el-progress v-if="loading" :percentage="progress"></el-progress> {{format}}
    <el-upload
        ref="upload"
        :headers="header"
        v-loading="loading"
        :key="uniqueKey"
        class="upload-logo"
        drag 
        action="resendArtUpload"
        :show-file-list="false"
        :on-success="handleSuccess"
        :on-error="handleError"
        :on-progress="handleProgress"
        :before-upload="beforeUpload">
        <img v-if="content.thumbnail" :src="content.thumbnail" class="avatar">
        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
</div>
</template>

<script>
export default {
    props: ['label','uniqueBoxLabel','disable'],
    data() {
        return {
            progress : 0,
            position : null,
            header : {"X-CSRF-TOKEN":this.$root.csrf_token},
            allowed_extension : ["jpg","jpeg","png","psd","cdr","pdf","ai","eps"],
            uniqueKey : +new Date,
            content : {
                thumbnail  : null,
                file  : null,
                label : this.label
            },
            loading : false
        }
    },
    computed : {
        format() {
            let percentage = this.progress
            if(!this.loading)
                return ""
            percentage = percentage == 100 ? "Processando ..." : "Enviando arquivo"
            return percentage
        }
    },
    methods: {
        handleProgress(e) {
            if (e.lengthComputable) {
                this.progress = parseInt( Math.round( ( e.loaded * 100 ) / e.total ) )
            }
        },
        init() {
            this.$parent.toggleMultiple(false,false)
            this.$parent.toggleUnique(false,false)
            this.$parent.files = []
        },
        deleteFile() {
            this.loading = true
            this.$parent.removeFromFiles(this.position)
            this.content.file = null
            this.content.thumbnail = null
            this.loading = false
            this.init()
        },
        handleError(e) {
            this.loading = false;
            let data = e.response.data.errors
            let message = "<ul>"
            for(let i in data) {
                message +=`<li>${data[i]}</li>`
            }
            message +="</ul>"
            return this.$toastr.error(message)
        },
        getThumbnail(data) {
            let image = data.raw_url
            let extension = this.getExtension(image)
            switch(extension) {
                case "psd":
                    return this.$root.root_url+"/assets/images/psd_icon.png"
                break
                case "cdr":
                    return this.$root.root_url+"/assets/images/cdr_icon.png"
                break
                case "pdf":
                    return this.$root.root_url+"/assets/images/pdf_icon.png"
                break
                case "ai":
                    return this.$root.root_url+"/assets/images/ai_icon.png"
                break
                case "eps":
                    return this.$root.root_url+"/assets/images/eps_icon.png"
                break
                default :
                    return image
                break
            }
        },
        handleSuccess(res, file) {
            if(!res.success) {
                this.loading = false
                return this.$toastr.error(res.message)
            }
            let thumbnail = this.getThumbnail(res.data)
            this.content.file = res.data
            this.content.thumbnail = thumbnail
            this.updateInfo()
            return this.loading = false
        },
        getExtension(name) {
            return name.substring(name.length-3,name.length).replace(".","")
        },
        beforeUpload(file)  {
            let extension = this.getExtension(file.name)
            this.loading = true
            this.progress = 0
            if (!this.allowed_extension.includes(extension)) {
                this.$toastr.error(`Extensão de arquivo não permitida, apenas ${this.allowed_extension.join(", ")}`)
                return this.loading = false
            }
            return true
        },
        updateInfo() {
            if(this.content.label == this.uniqueBoxLabel) 
                this.$parent.toggleMultiple(true)
            else 
                this.$parent.toggleUnique(true)
            this.position = this.$parent.addFile(this.content)
        }
    }
}
</script>

<style lang="scss">
  .avatar-uploader 
  {
        &.el-upload 
        {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
    }

    .avatar-uploader
    {
        &.el-upload:hover 
        {
            border-color: #409EFF;
        }
        .el-upload 
        {
            &.el-upload--text 
            {
                display: flex !important;
            }
        }
    }
    .avatar-uploader-icon 
    {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar 
    {
        background-repeat: no-repeat;
        background-position: center; 
        background-size: 100% auto;
    }
    .delete-link 
    {
        color: red;
        text-decoration: underline !important;
        cursor: pointer;
    }
    .el-upload-dragger 
    {
        flex-direction: unset!important;
        
    }
    .overlay {
        height: 148px;
        width: 148px;
        background-color: #eaeaea;
        position: absolute;
        opacity: .7;
        z-index: 9999;
    }
</style>
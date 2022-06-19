<template>
  <div class="row" type="flex" justify="start" id="join-us-confirm">
    <el-form-item :prop="i" required class="mx-3" :label="docNames[i]" v-for="(file, i) in userFiles" :key="i">
      <upload-input :key="i" v-model="userFiles[i]"/>
    </el-form-item>
  </div>
</template>
<script>
import UploadFile from "./-UploadFile"

export default {
  components: { 'upload-input': UploadFile },
  props: ['type', '_company', '_reseller','_files','_form'],
  data() {
    return {
      fileTypes: [{ identity: '' }, { ie: '' }],
      formFiles: this._files
    }
  },
  mounted() {
    this.filesToUpload()
  },
  methods: {
    filesToUpload() {
      let list = this.userFiles
      this.$set(list, 'doc', '')
      !this.noIm ?
        this.$set(list, 'im', '') //se pj com inscricao
        : this.type ? ''
          : this.$set(list, 'identity', '')
    },
    validateUploads(){
      if(!this._form) return
      
      let v = [],
      files = this.userFiles

      for(let k in files)
        files[k] ? v.push(k) : ''
      
      
      v.length ? this._form.validateField(v) : ''
    }
  },
  computed: {
    noIm() {//inscricao municipal
      return this._company.no_im
    },
    userFiles() {
      return this.fileTypes[this.type]
    },
    docNames() {
      return {
        doc: this.type ? "CNPJ" : "CPF",
        im: "Inscrição Municipal",
        ie: "Inscrição Estadual",
        identity: "Identidade"
      }
    }
  },
  watch:{
    fileTypes:{
      deep:true,
      handler(){
        Object.assign(this.formFiles,this.userFiles)
        this
        .$emit('update')
        .$nextTick(this.validateUploads)
      }
    }
  }
}
</script>
<style lang="scss">
#join-us-confirm {
  .el-upload {
    width: 148px;
    &.el-upload--picture-card {
      overflow: hidden;
      position: relative;
    }
    .el-upload-list__item-actions {
      position: absolute;
      width: 100%;
      top: 0;
      height: 100%;
    }
  }
}
</style>
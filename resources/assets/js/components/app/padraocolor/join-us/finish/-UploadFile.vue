<template>
  <el-upload
    drag
    :class="{'file-custom-upload':1,'has-image' : image}"
    action="#"
    list-type="picture-card"
    :auto-upload="false"
    :multiple="false"
    :on-change="onChange"
    ref="file"
  >
    <i slot="trigger" class="el-icon-plus align-self-center" />
    <div slot="file" slot-scope="{file}">
      <el-image class="el-upload-list__item-thumbnail" :src="image" fit="cover" />
      <span class="el-upload-list__item-actions">
        <span class="el-upload-list__item-delete" @click="handleRemove(file)">
          <i class="el-icon-delete text-white" />
        </span>
      </span>
    </div>
  </el-upload>
</template>
<script>
export default {
  props:['value'],
  data() {
    return {
      image: '',
      allowedMimes: ['image/jpg', 'image/jpeg', 'image/png', 'application/pdf']
    }
  },
  methods: {
    handleRemove(file) {
      setTimeout(z => this.image = '', 450)
      this.$refs.file.uploadFiles = []
      this.$emit('input','')
    },
    validUpload(file) {
      let sizeLimit = 8,
        oversized = file.size / 1024 / 1024 > sizeLimit,
        typeAllowed = this.allowedMimes.includes(file.raw.type)

      try {

        if (oversized)
          throw new Error(`Arquivos suportados até ${sizeLimit} mb`)

        if (!typeAllowed)
          throw new Error("Aceitamos apenas arquivos do tipo " + this.allowedMimesText)

      } catch (e) {
        this.$message.error(e.message)
        this.handleRemove(file)
        return
      }

      return true
    },
    onChange(file) {
      return this.validUpload(file)
        && (this.image = file.url) && this.$emit('input',file.raw)
    },
  },
  computed: {
    allowedMimesText() {
      return this.allowedMimes
        .map(t =>  t.split("/")[1])
        .join(", ");
    }
  }
}
</script>
<style lang="scss">
.file-custom-upload {
  .el-upload-list--picture-card {
    .el-upload-list__item {
      height: fit-content !important;
      .el-image {
        margin-bottom: -10px;
      }
    }
  }
  &.has-image {
    > .el-upload {
      display: none;
    }
  }
  .el-upload-dragger {
    background-color: unset;
    border: none;
    height: 148px;
  }
}
</style>
<template>
  <el-upload
  class="upload-files"
  drag
  action="https://jsonplaceholder.typicode.com/posts/"
  :auto-upload="false"
  list-type="picture"
  multiple>
  <img class="mb-3" src="/svg/upload/cloud-upload.svg">

  <div class="el-upload__text f-16 f-space">Arraste o arquivo aqui ou <em class="text-secondary font-weight-bold">clique para fazer o upload</em></div>
  <div class="el-upload__tip" slot="tip">
    <p class="f-12 f-space g-6">Formatos aceitos*</p>
    <div class="mimes">
      <p>PNG</p>
      <p>PDF</p>
      <p>DOCX</p>
    </div>
  </div>
</el-upload>
</template>

<style lang="scss">
.mimes{
  display: flex;
  flex-wrap: wrap;
  >p{
    padding: 6px 20px;
    border-radius: 50rem;
    border: 1px solid #D5D5D5;
    margin-right: 12px;
    &:last-child{
      margin-right: 0px;
    }
  }
}
.el-upload-list__item{
  box-shadow: #00000029 0px 0px 5px;
  display: flex;
  flex-wrap: wrap;
  flex-direction: column;
  justify-content: space-between;
  height: 177px!important;
  padding: 0px!important;
  align-items: center;
  width: 146px;
}
.el-upload-list__item-name{
  flex-grow: 1;
  background-color: #F2F2F2;
  align-self: start;
  margin:0;
  margin-top:0!important;
  padding: 4px 30px 0 2px;
  width: 144px;
}
.el-icon-close{
  z-index: 3;
  top: unset!important;
  bottom: 6px !important;
  right: 6px!important;
  font-family: "Material Icons"!important;
  font-size: 20px;
  &::before{
    content:"\E872";
  }
}
.el-upload-list__item-thumbnail{
  margin-left:0!important;
}
.el-upload-list--picture .el-upload-list__item-thumbnail{
  width: 140px;
  height: 140px;
  margin-top: 2px;
  float: left;
  position: relative;
  z-index: 1;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
}
.el-upload-dragger{
  display: flex;
  justify-content: center;
  flex-direction: column;
}
.el-upload-list{
  display: flex;
}
.el-icon-document{
  display:none;
}
.upload-files,.el-upload-dragger,.el-upload{
  width:100%;
}
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409EFF;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
</style>

<script>
export default {
  data() {
    return {
      imageUrl: ''
    };
  },
  methods: {
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw);
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg';
      const isLt2M = file.size / 1024 / 1024 < 2;

      if (!isJPG) {
        this.$message.error('Avatar picture must be JPG format!');
      }
      if (!isLt2M) {
        this.$message.error('Avatar picture size can not exceed 2MB!');
      }
      return isJPG && isLt2M;
    }
  }
}
</script>

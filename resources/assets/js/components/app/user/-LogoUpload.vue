<style lang="scss">
.upload-logo {
    .el-upload-dragger {
      align-items: center;
    }
}
</style>
<template>
    <el-upload v-if="!hideIfExists || (hideIfExists && !imageUrl)"  v-loading="loading" :headers="header"  class="upload-logo" drag :action="postUrl" :before-upload="beforeAvatarUpload" :show-file-list="false" :on-success="handleAvatarSuccess">
          <img v-if="imageUrl" :src="imageUrl" class="avatar">
          <template v-else>
              <img class="mb-3" src="/svg/upload/cloud-upload.svg">
              <slot></slot>
          </template>
    </el-upload>
</template>

<script>
export default {
  props:['hideIfExists'],
  data() {
    return {
      header : {"X-CSRF-TOKEN":this.$root.csrf_token},
      getUrl : this.$root.root_url+"/api/reseller/get_logo_url",
      postUrl : this.$root.root_url+"/api/reseller/post_logo_url",
      imageUrl: '',
      loading : false
    };
  },
  mounted() {
    this.loadLogo()
  },
  methods: {
    loadLogo() {
        this.loading = true
        this.$http.get(this.getUrl,null).then( response => {
            response = response.data
            if(response.success) {
                this.imageUrl = response.data
            }
        })
        .finally( e => 
            this.loading = false
        )
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = res.data;
      this.loading = false
    },
    beforeAvatarUpload(file) {
      this.loading = true
    }
  },
  watch:{
    imageUrl(v){
      this.$emit('has-logo',!!v)
    }
  }
}
</script>

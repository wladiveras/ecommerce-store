<template>
  <div id="vue-portal-container" style="display:none;">
    <div id="vue-portal-wrapper">
      <div id="vue-portal-content">
        <slot></slot>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
.sweet-portal{
  padding:0px;
  &.swal-full-width{
    width: 80%!important;
  }
  .swal2-header{
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    padding: 20px 17px;
    background-color: #F2F2F2;
    flex-direction:row;
    color: #313131;
    .swal2-title{
      font-size:18px;
      margin-bottom:0px;
      text-transform: uppercase;
    }
    .swal2-close{
      color: #313131;
      font-weight: bold;
      font-size:30px;
      top:12px;
      right: 5px;
    }
  }
  #swal2-content{
    text-align: left;
    padding:20px;
  }
}
</style>
<script>
export default {
  props:{
    trigger:null,
    title:null,
    size:{
      default: 'normal',
      validator: function (value) {
        // The value must match one of these strings
        return ['full-width','normal'].indexOf(value) !== -1
      }
    }
  },
  watch:{
    trigger(){
      if(this.trigger){
        this.displayInModal();
      }else{
        this.closeModal();
      }
    }
  },
  methods:{
    moveVue(cargo,from, options = {}){
      let data = {
        html: `<div id="vue-modal-content"><div>`,
        customClass:`sweet-portal text-left swal-${this.size}`,
        showCancelButton: false,
        showConfirmButton: false,
        onClose(){
          $(cargo).appendTo(from);
        }
      };
      data = Object.assign(data, options);
      let res = swal(data);
      from.children().appendTo("#vue-modal-content");
      return res;
    },
    closeModal(){
      $("#vue-portal-content").appendTo($("#vue-portal-wrapper"));
      swal.close();
    },
    displayInModal(){
      let template = ``;
      let modal = this.moveVue("#vue-portal-content",$("#vue-portal-wrapper"),{
        showCloseButton: true,
        title:this.title
      }).then(function(){
        this.$emit('close',false)
      }.bind(this));
      this.$emit('open',true)
    }
  }
}
</script>

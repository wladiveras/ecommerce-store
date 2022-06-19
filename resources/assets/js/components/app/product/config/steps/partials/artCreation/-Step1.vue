<style scoped>
.term-content
{
  max-height: 366px;
  overflow: auto;
  color: #9e9e9e;
}
</style>
<template>
  <div>
    <div class="row">
      <div class="col-md-12 text-left term-content">
        <div v-html="termsText"> </div>
      </div>
    </div>
  </div>
</template>

<script>
import terms from "./terms.js"
export default {
  props: ["shared"],
  data() {
    return {
      terms
    }
  },
  mounted()
  {
    this.$parent.$parent.btn_text = "ACEITO";
    this.shared.data.art_creation_info.accepted_termos = false;
  },
  methods :
  {
    submit()
    {
      this.shared.data.art_creation_info.accepted_termos = true;
      this.$parent.position ++;
    }
  },
  computed:{
    termsText(){
      let preview = this.terms;
        //preview = preview.replace(/(?:\r\n|\r|\n)/g, '<br/>');
      return preview
    }
  }
}
</script>

<template>
  <select :multiple="multiple" :value="value"><slot></slot></select>
</template>

<script>
export default {
  props:{
    value: [String, Array, Number],
    multiple: Boolean
  },
  template:`<select :data-placeholder="placeholder" :multiple="multiple" :value="value"><slot></slot></select>`,
  methods:{
    update(){
      $(this.$el).trigger('chosen:updated');
      $(this.$el).trigger('liszt:updated');
    }
  },
  mounted(){
    let chosen =  $(this.$el);

    if(this.value){
      chosen.val(this.value);
    }
    chosen.chosen()
    .on("change", e => this.$emit('input', $(this.$el).val()))

    this.observer = new MutationObserver(function(mutations) {
      $(this.$el).val(this.value);
      this.update();
      this.number++;
    }.bind(this));
    this.observer.observe(
      $(this.$el)[0],
      { attributes: true, childList: true, characterData: true, subtree: true }
    );
  },
  watch:{
    value(val){
      $(this.$el).val(val);
      this.update();
    }
  },
  destroyed() {
    this.observer.disconnect();
    $(this.$el).chosen('destroy');
  }
}
</script>

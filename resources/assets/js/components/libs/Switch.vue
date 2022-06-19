<template>
  <div class="display">
    <label class="label toggle">
      <input type="checkbox" @change="update" class="toggle_input" v-model="data"/>
      <div class="toggle-control"><span class="unselectable">{{text}}</span> </div>
    </label>
  </div>
</template>

<script>
export default {
  props:{
    value: Boolean,
    text: String
  },
  data(){
    return {
      data: false
    }
  },
  mounted(){
    this.data = this.value;
  },
  methods:{
    update() {
      this.$emit('input', this.data);
    }
  },
  watch:{
    value(val){
      this.data = this.value;
    }
  }
}
</script>

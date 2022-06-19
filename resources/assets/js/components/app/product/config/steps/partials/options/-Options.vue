<template>
  <div v-loading="loading" v-if="options" :class="{'has-measures': this.shared.hasMeasures && this.shared.isPreset}">
    <a
      href="#"
      id="reset-options"
      @click.prevent="reset"
      class="float-right d-flex align-items-center"
    >
      <i class="material-icons mr-2">refresh</i> Redefinir opções
    </a>
    <template v-for="(attribute,step) in options">
      <card-items :key="attribute.key"
        v-if="shared.step.show_card_attributes"
        :shared="shared"
        :attribute="attribute"
        :variation="attribute.key"
        :step="step"
        :data="data"
      />
    </template>
    <p class="mb-4">
      <template v-if="($parent.ready && messages.length>0)">
        <div class="alert alert-warning mt-0" role="alert" v-for="message in messages">
          <div class="d-flex align-items-center">
            <i class="material-icons mr-3">error</i>
            <span v-html="message"></span>
          </div>
        </div>
      </template>
    </p>
    <modal name="modal_attribute_info" trasition="fade" height="auto" width="600" :adaptive="true">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between">
                <b>{{modal_info.name}}</b>
                <a href="#" @click.prevent="$modal.hide('modal_attribute_info')">
                  <i class="material-icons">close</i>
                </a>
              </div>
            </div>
            <div class="card-body p-1">
              <div class="row d-flex align-items-center" style="padding: 10px 20px 10px 20px;">
                <div
                  v-bind:class="{'col-md-6  col-sm-12' : modal_info.image,'col-12':!modal_info.image}"
                >
                  <p
                    style="font-size: 15px;"
                    v-html=" modal_info.description ? modal_info.description.replace(/\r?\n/g, '<br />') : ''"
                  ></p>
                </div>
                <div
                  class="d-flex align-items-center"
                  v-if="modal_info.image"
                  v-bind:class="{'col-md-6  col-sm-12' : modal_info.description,'col-12':!modal_info.description}"
                  style="position:relative;"
                >
                  <img :src="modal_info.image" class="w-100" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import _ from 'lodash'
export default {
  props: ['shared','data'],
  data() {
    return {
      loading: true,
      options: [],
      actual_step: 0,
      total_steps: -1,
      selected_options: [],
      messages: [],
      modal_info: {
        attribute: null,
        description: null,
        image: null,
      }
    }
  },
  components: {
    'old-card-items': require('./-OldItems.vue').default,
    'card-items': require('./-Items.vue').default,
  },
  async mounted() {
    if(this.shared.isPreset){
      let values = Object.values(this.shared.step.selected_options);
      this.selected_options = values
      this.total_steps = values.length + Boolean(this.shared.presetSkuQty)
      this.actual_step = this.total_steps -1
    }
    await this.load_options()
  },
  methods: {
    show_modal_info(attribute,detail) {
      this.modal_info.name = attribute
      this.modal_info.description = detail.description
      this.modal_info.image = detail.image ? `${detail.image}?width=415&height=328&gravity=center` : null
      this.$modal.show("modal_attribute_info")
    },
    reset() {
      this.actual_step = 0
      this.total_steps = -1
      this.$parent.confirmed = false
      this.options = []
      this.shared.data.options = {}
      this.selected_options = []
      this.shared.data.skus = null
      this.shared.data.sku = null
      this.$parent.ready = (this.actual_step == (this.total_steps - 1))
      this.load_options()
    },
    async select_option(option,step,index) {
      if (this.actual_step != step)
        this.clean_options(step,index)
      this.actual_step++
      this.selected_options.push(option)
      this.$parent.ready = (this.actual_step == (this.total_steps - 1))
      await this.load_options()
    },
    proccess_response(response) {
      var data = response.data
      this.data.has_qty = data.extra.qty.has_qty
      this.messages = data.messages
      if (data.final) {
        this.shared.data.sku = data.skus[0]
      }
      this.shared.data.sku_ids = data.sku_ids
      var key = data.key
      if (key) {
        this.options.push({
          key: key,
          attributes: data.options,
          details: data.details
        })
        this.total_steps = data.total_steps
      } else if (this.actual_step == this.total_steps) {
        this.$parent.ready = true
      }
      return this.loading = false
    },
    clean_options(step,index) {
      this.options.splice(step + 1,this.options.length)
      this.selected_options.splice(step,this.selected_options.length)
      this.clean_selected(index)
      this.actual_step = step
    },
    clean_selected(index) {
      let aux = {}
      for (let key in this.options) {
        aux[this.options[key].key] = this.shared.data.options[this.options[key].key]
      }
      this.shared.data.options = aux
    },
    async load_options() {
      this.loading = true
      await this.$http.post(this.$root.root_url + "/produtos/get_options",{
        product_id: this.data.product.id,
        option_step: this.actual_step,
        selected_options: this.selected_options
      }).then(response => {
        response = response.data
        if (!response.success) {
          this.loading = false
          return this.$toastr.error(response.message)
        }
        return this.proccess_response(response)
      })
    }
  }
}
</script>
<style lang="scss">
.has-measures{
  .variation-option{
    
  }
}
</style>
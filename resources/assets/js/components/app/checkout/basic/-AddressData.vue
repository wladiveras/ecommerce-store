<style lang="scss">

.card.address-data {
    background: transparent;
    border: none;
    input[disabled="disabled"].custom-form-checkout {
        border: none;
        background: transparent;
        color: #000;
    }
    .card-body {
        padding-top: 0;
    }
}
</style>
<template>
<div>
    <b-card no-body class="address-data" id="section_address_form" v-loading="loading">
        <!-- <b-card-header role="tab" v-b-toggle.addressdata class="section-completed">
            <i class="mi mi-check"></i>
            <span class="ml-2 f-18">Endereço</span>
        </b-card-header> -->
        <!-- <b-collapse ref="addresscolapse" id="addressdata" role="tabpanel" accordion="checkout"> -->
            <h5>Endereço de cobrança</h5>
            <b-card-body class="pb-3">
                <transition name="fade">
                    <div class="row" v-if="shared.enable_reseller_address_edit">
                        <div class="col-12">
                            <div class="alert alert-danger  mb-3" role="alert">
                                <ul class="mb-0 ">
                                    <li class="text-white">Digite um número válido para endereço do revendedor</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </transition>
                <div>{{fullAddress}} - {{addresssDistrict}}</div>
                <div>{{addresssCityUF}}</div>
                <div>CEP: {{addressZipcode}}</div>
                <!-- <div class="row"> -->
                    <!-- <div class="d-flex">
                        <b-input placeholder="Rua" disabled v-model="shared.user.address.street" class="custom-form-checkout" />
                        <span>,</span>                        
                        <b-input placeholder="Número" :disabled="!shared.enable_reseller_address_edit" v-model="shared.user.address.number" class="custom-form-checkout" />
                        <b-input placeholder="Complemento" :disabled="!shared.enable_reseller_address_edit" v-model="shared.user.address.complement" class="custom-form-checkout" />
                    </div> -->
                    <!-- <div class="d-flex">
                        <b-input placeholder="Bairro" disabled v-model="shared.user.address.district" class="custom-form-checkout" />
                        <b-input placeholder="Cidade" disabled v-model="shared.user.address.city" class="custom-form-checkout" />
                        <b-input placeholder="Estado" disabled v-model="shared.user.address.state" class="custom-form-checkout" />
                    </div>
                    
                    
                    <the-mask placeholder="CEP" mask="#####-###" disabled v-model="shared.user.address.zip_code" class="custom-form-checkout form-control flexb-6" /> -->

                 
                <!-- </div> -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" @click="updateForm" v-if="shared.enable_reseller_address_edit" :disabled="!shared.user.address.number" v-bind:class="{'shaking':shared.user.address.number}" class="btn btn-secondary float-right">Salvar Edição</button>
                    </div>
                </div>
                <slot></slot>
            </b-card-body>
        <!-- </b-collapse> -->
    </b-card>
</div>
</template>

<script>
export default {
    props: ['shared', 'form'],
    data() {
        return {
            loading : false
        }
    },
    mounted() {
        if(!this.form.address.number)
            this.$parent.$parent.breakInUpdateResellerAddres()
    },
    methods: {
        updateForm() {
            this.loading = true
            this.$http.post(`${window.location.href}/update_reseller_address`,this.form.address).then(response => {
                response = response.data
                if(!response.success)
                    this.$toastr.error(response.message)
                window.location.reload()
            }).catch(error => {
                console.log(error)
                this.loading = false
            })
        }
    },
    computed: {
    fullAddress: {
      get() {
        return `${this.shared.user.address.street}, ${this.shared.user.address.number} ${this.shared.user.address.complement}`;
      }
    },
    addresssDistrict: {
      get() {
        return `${this.shared.user.address.district}`;
      }
    },
    addresssCityUF: {
      get() {
        return `${this.shared.user.address.city}/${this.shared.user.address.state}`;
      }
    },
    addressZipcode: {
      get() {
        return `${this.shared.user.address.zip_code}`;
      }
    }
  }
}
</script>

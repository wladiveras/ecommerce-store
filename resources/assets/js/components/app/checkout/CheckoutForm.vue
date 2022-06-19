<style>
input[disabled="disabled"].custom-form-checkout,
select[disabled="disabled"].custom-form-checkout,
textarea[disabled="disabled"].custom-form-checkout {
    border: solid 1px #C7C7C7;
    background-color: #F2F2F2;
    color: #909090;
}

/* .section-completed {
    background-color: #D6EF63 !important;
    color: #1B4E01 !important;
} */

.payment-options-custom {
    border: 1px solid #C7C7C7;
    border-radius: 5px;
}

.btn-finish-custom {
    background-color: #477032;
    color: white;
    padding: 12px 0px;
    letter-spacing: 1px;
}
</style>
<template>
<div>
    <completed v-if="shared.completed" :shared="shared" />
    <user-form ref="userform" :_user="_user" :_route_store_order="_route_store_order" :_subtotal="totalPrice" :shared="shared" v-else></user-form>
</div>
</template>

<script>
export default {
    props: {
        _creditcard_config : {},
        _content: null,
        _order: null,
        _cards: null,
        _user: null,
        _route_store_order: null,
        _subtotal: null,
    },
    components: {
        'user-form': require('./-UserForm.vue').default,
        'completed': require('./-Completed.vue').default
    },
    computed : {
        totalPrice() {
            let content = this._content;
            let price = 0;
            for(let row in content) {
                price += Number(content[row].price);
            }
            return price;
        }
    },
    data() {
        return {
            shared: {
                credit_card_config : this._creditcard_config,
                cards : this._cards,
                enable_reseller_address_edit : false,               
                user: this._user,
                cart: this._content,
                completed: false,
                order: null,
                data: null,
                step: null,
                extraStep: null,
                shippingPrice: null,
                allowedPaymentMethods: [],
                shippingTypes: {
                    withdrawal: {
                        name: 'Retirada no Balcão',
                        time: 'previsão de entrega',
                        price: '',
                        rawPrice: 0
                    },
                    payment: {
                        name: 'Entrega pela Rota',
                        time: 'até 6 dias úteis',
                        price: 'Grátis',
                        rawPrice: null,
                    },
                    shipping: {
                        name: 'Frete',
                        time: 'até 3 dias úteis',
                        price: 'Variável',
                        rawPrice: null,
                        method:null
                    }
                }
            }
        }
    },
    methods: {
        breakInUpdateResellerAddres()
        {
            this.$refs.userform.$refs.addressdata.$refs.addresscolapse.toggle()
            this.$toastr.error("Complete o cadastro de endereço do revendedor para poder prosseguir")
            this.shared.enable_reseller_address_edit = true
            this.$scrollTo("#section_address_form")
        }
    }
}
</script>

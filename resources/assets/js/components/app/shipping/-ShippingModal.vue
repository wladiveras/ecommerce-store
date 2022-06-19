<template>
    <el-col class="shipping-form">
        <el-form label-position="top">
            <el-row>
                <el-col :md="24">
                    <el-row type="flex" justify="space-between">
                        <el-col>
                            <h4>Informações do Destinatário</h4>
                        </el-col>
                        <el-col v-if="mode == 'edit'">
                            <el-switch
                                v-model="address.type"
                                active-color="#13ce66"
                                inactive-color="#D6EF63"
                                inactive-text="Frete normal"
                                inactive-value="normal"
                                active-text="Direto para o Cliente"
                                active-value="client"
                            />
                        </el-col>
                    </el-row>
                </el-col>
                <el-col :md="12">
                    <el-form-item label="Destinatário" required>
                        <el-input
                            v-model="address.name"
                            required
                            placeholder="Nome da Empresa/Cliente/Pessoa"
                        />
                    </el-form-item>
                </el-col>
                <transition-group name="el-zoom-in-top">
                    <template v-if="[type,address.type].includes('client')">
                        <el-col :key="'clientDoc'" :md="6">
                            <el-form-item
                                required
                                :show-message="!validDoc"
                                :error="!validDoc ? `CPF/CNPJ inválido` : ``"
                                label="CPF/CNPJ"
                            >
                                <el-input
                                    v-model="address.client_doc"
                                    v-mask="['###.###.###-##', '##.###.###/####-##']"
                                    placeholder="CPF/CNPJ"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :key="'clientPhone'" :md="6">
                            <el-form-item label="Telefone" required>
                                <el-input
                                    v-model="address.client_phone"
                                    v-mask="['(##) ####-####', '(##) #####-####']"
                                    placeholder="Telefone ou Celular"
                                />
                            </el-form-item>
                        </el-col>
                    </template>
                </transition-group>
            </el-row>
            <el-row>
                <el-col :md="12">
                    <el-form-item label="Endereço" required>
                        <el-input
                            placeholder="CEP"
                            :show-message="validCep"
                            v-mask="`#####-###`"
                            v-model="cep"
                        />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input v-model="address.city" required placeholder="Cidade" />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input
                            v-model="address.state"
                            required
                            placeholder="Estado"
                            maxlength="2"
                        />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input v-model="address.district" required placeholder="Bairro" />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input v-model="address.street" placeholder="Rua" />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input
                            v-mask="`###########`"
                            v-model="address.number"
                            required
                            placeholder="Número"
                        />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item required>
                        <el-input v-model="address.complement" placeholder="Complemento" />
                    </el-form-item>
                </el-col>
                <el-col :md="8" required>
                    <el-form-item>
                        <el-input v-model="address.reference" placeholder="Referência" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row type="flex" justify="space-between">
                <el-col :md="12">
                    <el-form-item>
                        <b-form-checkbox v-model="saveAddress" v-if="false">
                            <div class="d-flex flex-wrap align-items-center">
                                <check-circle />
                                <span class="ml-3">Salvar Endereço</span>
                            </div>
                        </b-form-checkbox>
                        <el-button
                            v-if="address.id"
                            @click="erase"
                            class="f-14"
                            type="danger"
                        >Apagar Endereço</el-button>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item class="d-flex justify-content-end">
                        <el-button
                            class="f-14"
                            :disabled="!canProceed"
                            type="primary"
                            @click="send"
                        >Salvar</el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    </el-col>
</template>
<style lang="scss">
.el-form-item__label {
    font-weight: bold;
}
.el-switch__label--right {
}
</style>
<script>
import _ from 'lodash'
export default {
    props: {
        index: null,
        type: {
            default: 'normal'
        },
        mode: {
            default: 'new',
            validator: function (v) {
                return ['edit','select'].indexOf(v) !== -1
            }
        },
        bAddress: {
            default() {
                return {
                    id: '',
                    name: '',
                    type: '',
                    city: '',
                    district: '',
                    state: '',
                    zip_code: '',
                    number: '',
                    complement: '',
                    street: '',
                    reference: '',
                    data: { client_doc: '',client_phone: '' }
                }
            }
        }
    },
    data() {
        return {
            address: _.clone(this.bAddress),
            cep: '',
            saveAddress: true,
            validDoc: null,
            validCep: null,
            newCep: true,
        }
    },
    created() {
        this.cep = this.address.zip_code
    },
    methods: {
        send() {
            if (!this.canProceed) return

            this.request()
            if (!this.address.id)
                this.$emit('saved',this.address,this.saveAddress)
        },
        async request() {
            this.$http.post(`/api/user/addresses/new${(this.address.id) ? '/' + this.address.id : ''}`,{ ...this.address,type: this.address.type ? this.address.type : this.type })
                .then(res => {
                    this.$toastr.success(res.data.message)

                    if (this.address.id) {
                        this.$emit('updated')
                    } else {
                        this.$emit('new',res.data.data)
                        this.cep = ""
                        this.address = this.bAddress
                    }

                    if (this.mode == 'select') {
                        this.address.id = res.data.data.id
                    }

                })
                .catch(e => {
                    this.$emit('deleted',this.index)
                    return this.$toastr.error("Não foi possível salvar o endereço.</br>" + Object.values(e.response.data.errors)[0][0])
                })
        },
        erase() {
            this.$http.post(`/api/user/addresses/delete${(this.address.id) ? '/' + this.address.id : ''}`,this.address)
                .then((res) => {
                    if (res.error)
                        return this.$toastr.error("Não foi possível apagar o endereço")

                    this.$toastr.success(res.data.message)
                    this.address = Object.assign({},this.bAddress)
                    this.cep = ''
                    this.$emit('deleted',this.index)
                })
                .catch((e) => {
                    return this.$toastr.error("Não foi possível apagar o endereço")
                })
        },
        getAddress() {
            if (!this.newCep) return
            this.newCep = false
            let url = `https://viacep.com.br/ws/${this.address.zip_code}/json/`
            this.$http.get(url)
                .then((res) => {
                    if (res.data.erro) return this.$toastr.error("CEP inválido")
                    res = res.data
                    let location = this.address
                    location.district = res.bairro
                    location.city = res.localidade
                    location.street = res.logradouro
                    location.state = res.uf
                    this.validCep = true
                })
        }
    },
    computed: {
        canProceed() {
            let commonRules = this.validCep && !!this.address.number
            switch (this.type) {
                case 'client':
                    return this.validDoc && commonRules
                default:
                    return commonRules
            }
        }
    },
    watch: {
        bAddress() {
            if (this.mode == 'edit') {
                this.address = this.bAddress
                this.cep = this.address.zip_code
            }
        },
        'address.client_doc': function (v) {
            if (v) {
                this.validDoc = valida_cpf_cnpj(v)
            } else {
                this.validDoc = null
            }
            function verifica_cpf_cnpj(valor) {
                valor = valor.toString()
                valor = valor.replace(/[^0-9]/g,'')
                if (valor.length === 11) {
                    return 'CPF'
                }
                else if (valor.length === 14) {
                    return 'CNPJ'
                }
                else {
                    return false
                }
            }

            function calc_digitos_posicoes(digitos,posicoes = 10,soma_digitos = 0) {
                digitos = digitos.toString()
                for (var i = 0;i < digitos.length;i++) {
                    soma_digitos = soma_digitos + (digitos[i] * posicoes)
                    posicoes--
                    if (posicoes < 2) {
                        posicoes = 9
                    }
                }
                soma_digitos = soma_digitos % 11
                if (soma_digitos < 2) {
                    soma_digitos = 0
                } else {
                    soma_digitos = 11 - soma_digitos
                }
                var cpf = digitos + soma_digitos
                return cpf
            }

            function valida_cpf(valor) {
                valor = valor.toString()
                valor = valor.replace(/[^0-9]/g,'')
                var digitos = valor.substr(0,9)
                var novo_cpf = calc_digitos_posicoes(digitos)
                var novo_cpf = calc_digitos_posicoes(novo_cpf,11)
                if (novo_cpf === valor) {
                    return true
                } else {
                    return false
                }
            }

            function valida_cnpj(valor) {
                valor = valor.toString()
                valor = valor.replace(/[^0-9]/g,'')
                var cnpj_original = valor
                var primeiros_numeros_cnpj = valor.substr(0,12)
                var primeiro_calculo = calc_digitos_posicoes(primeiros_numeros_cnpj,5)
                var segundo_calculo = calc_digitos_posicoes(primeiro_calculo,6)
                var cnpj = segundo_calculo
                if (cnpj === cnpj_original) {
                    return true
                }
                return false

            }

            function valida_cpf_cnpj(valor) {
                var valida = verifica_cpf_cnpj(valor)
                valor = valor.toString()
                valor = valor.replace(/[^0-9]/g,'')
                if (valida === 'CPF') {
                    return valida_cpf(valor)
                }
                else if (valida === 'CNPJ') {
                    return valida_cnpj(valor)
                }
                else {
                    return false
                }
            }
        },
        cep(n,o) {
            this.address.zip_code = this.cep.replace('-','')
            if (this.cep && this.address.zip_code.length == 8) {
                this.getAddress()
            } else if (n.length < o.length) {
                let location = this.address
                location.district = ''
                location.city = ''
                location.street = ''
                location.state = ''
                this.validCep = null
                this.newCep = true
            }
        }
    }
}
</script>

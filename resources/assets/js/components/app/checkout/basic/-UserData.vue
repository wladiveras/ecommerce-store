<style lang="scss">

.card.user-data {
    background: transparent;
    border: none;
    input[disabled="disabled"].custom-form-checkout {
        border: none;
        background: transparent;
        color: #000;
    }
    .card-body {
        padding-bottom: 0;
    }
}
</style>
<template>
<div>
    <b-card no-body class="user-data d-none d-lg-block">
        <!-- <b-card-header role="tab" v-b-toggle.userdata class="section-completed">
            <i class="mi mi-check"></i>
            <span class="ml-2 f-18">Dados Pessoais</span>
        </b-card-header> -->
        <!-- <b-collapse id="userdata" role="tabpanel" accordion="checkout"> -->
            <h4 class="d-flex align-items-baseline">Dados Pessoais <img src="/assets/images/icon-person-chekchout.jpg" alt="" style="max-width: 100%;"></h4>
            <b-card-body>
                <!-- <div class="row mb-3">
                    <div class="col-sm-12 flex-wrap d-sm-flex flexb-6-child justify-content-between"> -->
                        <div class="d-flex">
                            <label>Nome: </label><b-input placeholder="Nome" v-model="shared.user.name" class="custom-form-checkout" disabled></b-input>
                        </div>
                        <!-- <div class="d-flex">
                            <label>CPF: </label><b-input placeholder="CPF" v-model="shared.user.doc" class="custom-form-checkout" v-mask="['###.###.###-##', '##.###.###/####-##']" disabled></b-input>
                        </div> -->
                        <div class="d-flex">
                            <label>E-mail: </label><b-input placeholder="Email" v-model="shared.user.email" class="custom-form-checkout" disabled></b-input>
                        </div>
                        <div class="d-flex">
                            <label>Telefone: </label><b-input placeholder="Telefone" v-model="shared.user.phone" v-mask="['(##) ####-####', '(##) #####-####']" class="mb-3 custom-form-checkout" disabled></b-input>
                        </div>
                        
                    <!-- </div>
                </div> -->
                <slot></slot>
            </b-card-body>
        <!-- </b-collapse> -->
    </b-card>
</div>
</template>

<script>
export default {
    props: ['shared', 'form']
}
</script>

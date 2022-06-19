<style scoped>
.personal-info-content
{

}
textarea
{
    resize: none;
    overflow:auto;
}
</style>
<template>
<div>
    <form class="personal-info-content" v-on:submit.prevent="form_submit" >
        <div class="row mb-md-4">
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" class="form-control" placeholder="Nome da Empresa ou da Pessoa*" v-model="personal_info['name']" :required="enable_required"/>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="email" class="form-control" placeholder="Email*" v-model="personal_info['email']" :required="enable_required" />
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <input type="text" class="form-control" placeholder="Endereço Web" v-model="personal_info['web_address']" />
            </div>
        </div>
        <div class="row mb-md-4">
            <div class="col-md-4 mb-2 mb-md-0">
                <the-mask placeholder="Telefone*"  required :mask="['(##) ####-####', '(##) #####-####']" v-model="personal_info['phone']" class="form-control"  />
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <the-mask placeholder="Celular*"  required :mask="['(##) ####-####', '(##) #####-####']" v-model="personal_info['cell_phone']" class="form-control" :required="enable_required" />
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <input type="text" class="form-control" placeholder="Endereço*" v-model="personal_info['address']" :required="enable_required"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <textarea class="form-control" v-model="personal_info['extra']" rows="8" placeholder='Outra informação que queira inserir no design ?'>
                </textarea>
            </div>
        </div>
        <button type="submit" style="display:none;" ref="btn_form_submit">SUBMIT</button>
    </form>
</div>
</template>

<script>
export default {
    props: ["shared"],
    data() {
        return {
            personal_info: {},
            enable_required : true
        }
    },
    watch: {
        personal_info(val)
        {
            this.shared.data.art_creation_info.personal_info = val;
        }
    },
    mounted()
    {
        this.$parent.$parent.btn_text = "Continuar";
    },
    methods :
    {
        form_submit()
        {
            this.$parent.position ++;
        },
        submit()
        {
            $(this.$refs.btn_form_submit).click();
        }
    }
}
</script>

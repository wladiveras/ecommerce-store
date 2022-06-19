<style scoped>
textarea
{
    resize: none;
    overflow:auto;
}
</style>
<template>
<div>
    <form class="personal-info-content" v-on:submit.prevent="form_submit" >
        <div class="row mb-4">
            <div class="col-md-8"> 
                <el-select
                    v-loading="loading"
                    style="width:100%"
                    v-model="company_info['category']"
                    filterable
                    default-first-option
                    placeholder="Selecione a categoria da sua empresa*">
                    <el-option
                        v-for="(item,index) in categories"
                        :key="index"
                        :label="item"
                        :value="item">
                    </el-option>
                </el-select>
            </div> 
        </div>
        <div class="row mb-4">
            <textarea class="form-control" v-model="company_info['description']" rows="8" placeholder='Faça uma descrição sobre sua empresa, produto e público alvo.*' :required="enable_required">
            </textarea>
        </div>
        <span style="color: #1B4E01;">Envie arquivos de referência (jpg,png.gif,psd ou pdf)</span>
        <component-upload-area :shared="shared" />
        <button type="submit" style="display:none;" ref="btn_form_submit">SUBMIT</button>
    </form>
</div>
</template>

<script>
export default {
    props: ["shared"],
    data() {
        return {
            company_info: {},
            enable_required : true,
            categories : [],
            loading : false
        }
    },
    components: {
        'component-upload-area': require('./-UploadArea.vue').default,
    },
    watch: {
        company_info(val)
        {
            this.shared.data.art_creation_info.company_info = val;
        }
    },
    mounted()
    {   
        this.load_categories();
        this.$parent.$parent.btn_text = "Continuar";
    },
    methods : 
    {
        load_categories()
        {
            this.loading = true;
            this.$http.post(this.$root.root_url + "/api/produto_config/get_company_categories", {} ).then( (response)=>
            {
                response = response.data;
                if (!response.success) {
                    this.loading = false;
                    return this.$toastr.error(response.message);
                }
                this.categories = response.data;
                this.loading=false;
            })
            .catch(()=>{
                this.loading=false;
            });
        },
        form_submit()
        {
            this.$parent.position ++;
        },
        submit()
        {
            $(this.$refs.btn_form_submit).click();
        },
        next_step()
        {
            this.confirmed = true;
            this.shared.step.position++;
        }
    }
}
</script>

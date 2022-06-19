<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <b>Novo Projeto Especial</b>
                </div>
                <div class="card-body">
                    <div class="special_orders">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-md-8 col-sm-12">
                                    <label class="mb-0">
                                        <b>Título do Projeto :</b>
                                        <span class="text-danger" ref="input_title_error"></span>
                                    </label>
                                    <input class="form-control" v-model="frm.title">
                                    <small class="form-text text-muted">De um nome ao seu projeto</small>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="mb-0">
                                        <b>Data de Recebimento :</b>
                                        <span class="text-danger" ref="input_target_date_error"></span>
                                    </label>
                                    <el-date-picker
                                        class="w-100"
                                        v-model="frm.target_date"
                                        :format="'dd/MM/yyyy'"
                                        value-format="yyyy-MM-dd"
                                        type="date">
                                    </el-date-picker>
                                    <small class="form-text text-muted">Defina a data que deseja receber o produto</small>
                                </div>
                            </div>
                            <div class="row d-flex align-items-center mt-3">
                                <div class="col-12">
                                    <b>Descreva seu projeto em detalhes :</b>
                                    <span class="text-danger" ref="input_description_error"></span>
                                </div>
                                <div class="col-12">
                                    <component-summernote
                                        name="html"
                                        disableresize
                                        required
                                        :value="frm.description"
                                        v-model="frm.description"
                                    />
                                    <small class="form-text text-muted">Descreva de maneira completa aqui seu ticket</small>
                                </div>
                            </div>
                            <div class="row d-flex align-items-center mt-3">
                                <div class="col-12">
                                    <b>Upload de Arquivos de Referência :</b>
                                </div>
                                <div class="col-12">
                                    <el-upload :limit="5" class="upload-area" drag 
                                        v-loading="!can_submit"
                                        :headers="headers"
                                        :before-upload="beforeUpload"
                                        :action="data.routes.upload_image"
                                        :on-success="handleUploadSuccess"
                                        :on-remove="handleRemove" :file-list="frm.uploads" multiple>
                                        <div class="d-flex flex-column">
                                            <div v-if="frm.extra.uploads.length>=5" class="el-upload__text" >Você já selecionou 5 arquivos</div>
                                            <template v-else>
                                                <div><i class="el-icon-upload"></i></div>
                                                <div class="el-upload__text">Arraste seus arquivos aqui ou<em> clique para seleciona-los</em></div>
                                            </template>
                                        </div>
                                    </el-upload>
                                    <small class="form-text text-muted">Efetue upload de arquivos que deseja definir como referência, lembrando que o limite é 5 arquivos.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                       <div class="col-12 d-flex justify-content-end px-0">
                           <button class="btn btn-sm-block btn-primary" type="button" @click="submitForm" :disabled="!can_submit" >ENVIAR PROJETO ESPECIAL</button>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ['data'],
    data() {
        return {
            headers : {"X-CSRF-TOKEN":this.$root.csrf_token},
            frm_dialog_visible : false,
            can_submit : true,
            loading : null,
            frm : {
                title : null,
                description : null,
                target_date : null,
                extra : {
                    uploads : []
                }
            }
        }
    },
    methods : {
        validateForm() {
            let messages = []
            if(!this.frm.title) messages.push({ref:"input_title_error",message:"Campo Obrigatório"})
            if(!this.frm.target_date) messages.push({ref:"input_target_date_error",message : "Campo Obrigatório"})
            if(!this.frm.description) messages.push({ref:"input_description_error", message: "Campo Obrigatório"})
            return messages
        },
        beforeUpload() {
            this.can_submit = false
        },
        handleUploadSuccess(res, file) {
            this.frm.extra.uploads.push(res.data)
            this.can_submit = true
        },
        handleRemove(val) {
            this.frm.extra.uploads = this.frm.uploads.filter( x =>  {
                return x.id !== val.id
            })
        },
        submitForm() {
            let valid = this.validateForm()
            if(valid.length>0) {
                for(let i in valid) {
                    console.log(this.$refs[valid[i].ref].innerText = valid[i].message )
                }
                return this.$toastr.error("Preencha o formulário corretamente")
            }

            this.loading = this.$loading()
            this.$http.post(this.data.routes.store,this.frm).then(res => {
                res = res.data
                window.location.href=res.data.route
            }).catch( er => {
                let message = "<ul>"
                let errors  = er.response.data.errors
                for(let i in errors) {
                    message+=`<li>${errors[i]}</li>`                    
                }
                message+="</ul>"
                this.$toastr.error(message)
                this.loading.close()
            })
        }
    }
}
</script>
<style lang="scss">
    .upload-area {
        .el-upload-list__item {
            height : unset!important;
            margin-top: 5px!important;
        }
    }
</style>
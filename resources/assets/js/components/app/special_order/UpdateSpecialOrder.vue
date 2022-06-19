<template>
    <el-timeline-item placement="top">
        <div class="card mb-5 shadow">
            <div class="card-header"><b>Nova Atualização</b></div>
            <div class="card-body"> 
                <div class="row">
                    <div class="col-12">
                        <label><b>Novo Status</b></label>
                        <select class="form-control" v-model="frm.new_status">
                            <option value=""></option>
                            <option v-for="status in data.status_list" :value="status.id" v-if="status.id != data.status_id">{{status.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end">
                        <el-switch
                            v-model="add_comment"
                            active-text="Adicionar Comentário">
                        </el-switch>
                    </div>
                </div>
                <div class="row mt-3" v-if="add_comment">
                    <div class="col-12">
                        <component-summernote
                            name="html"
                            disableresize
                            :value="frm.comment"
                            v-model="frm.description"
                        />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" @click="save" :disabled="!frm.new_status">Salvar Alteração</button>
                    </div>
                </div>
            </div>
        </div>
    </el-timeline-item>
</template>
<script>
export default {
    props:["data"],
    data() {
        return {
            add_comment: false,
            frm : {
                comment : null,
                new_status : null
            }
        }
    },
    watch : {
        "frm.new_status"(val,old) {
            if(this.data["status_list"].find(x => (x.name =="Efetivado" && x.id == val))) {
                this.$swal.raw_confirm("Confirmação","O pagamento do projeto foi efetuado ?","warning", res => {
                    if(res.dismiss) this.frm.new_status = old
                })
            }
        }
    },
    methods : {
        save() {
            if(!this.add_comment) this.frm.comment = null
            this.$swal.confirm("Confirmação","Deseja salvar alteração","warning", _ => {
                this.$loading()
                this.$http.put(this.data.routes.put,this.frm).then( _ => window.location.reload() )
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.shadow {
    box-shadow: 0 0.4rem 1.8rem 0.2rem rgba(83, 117, 153, 0.3);
}
</style>
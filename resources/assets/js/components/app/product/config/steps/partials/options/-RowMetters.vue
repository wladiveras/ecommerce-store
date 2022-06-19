<template>
<div>
    <template>
        <div class="d-flex align-items-center justify-content-center">
           <div class="col-md-4 px-0 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <label class="labelMeasures">Largura</label>
                        <input placeholder="Digite a Largura" class="form-control inputMeasure" v-model="width" type="number" min="0" v-bind:class="{'inputError' : width<=0 }" :disabled="confirmed">
                        
                        <small>
                            <span v-if="confirmed">Clique em <b>Editar Medidas</b> para desbloquear os campos</span>
                            <span v-else>Digite em ambos os campos a medida em <b>{{completeUnit}}s</b></span>
                        </small>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                       <label class="labelMeasures">Altura</label>
                        <input placeholder="Digite a Altura" class="form-control inputMeasure" v-model="height"  type="number" min="0" v-bind:class="{'inputError' : height<=0 }" :disabled="confirmed">
                        <small>
                            <span v-if="confirmed">Clique em <b>Editar Medidas</b> para desbloquear os campos</span>
                        </small>
                    </div>
                </div>
           </div>
           <!--<div class="col-md-4 col-sm-12 text-center">
                <h4>SELECIONE A UNIDADE DE MEDIDAS</h4>
                <small>A mesma unidadae será aplicada para altura e largura</small>
                <hr style="border-top: 1px solid #d3d3d3!important;margin10px!important;">
                <label><input type="radio" value="cm" :disabled="confirmed" v-model="unit" class="mr-2" > <b class="labelMeasures">Centimetros</b></label>
                <label class="ml-2"><input type="radio" :disabled="confirmed" value="m" v-model="unit" class="mr-2"> <b class="labelMeasures">Metros</b></label>
            </div>-->
            <div class="col-md-8 col-sm-12 d-flex justify-content-center align-items-center" >
                <template v-if="(width>0.001 && height>0.001)">
                    <span class="height mr-1"><b>{{formatNumber(height)}} {{unit}}</b></span>
                    <div class="model d-flex align-items-center justify-content-center" v-bind:style="{'width':`${scale.width}px`, 'height':`${scale.height}px`}">
                        <span class="width"><b>{{formatNumber(width)}} {{unit}}</b></span>
                        <div class="text-center"><b class="text-danger">Exemplo em Escala</b></div>
                    </div>
                    <img class="ml-2" src="/assets/images/curly.png" style="width:100px"/>
                    <h5><span v-html="area"></span></h5>
                </template>
                <template v-else>
                    <h4 class="text-danger text-center shaking">Digite largura e altura válidas</h4>
                </template>
            </div>
        </div>
        <div class="row">
            <div class="col-12 px-0">
                <div role="alert" class="alert alert-warning mt-2 mb-2" v-if="((this.height>0.001)&&(this.width>0.001))">
                    <div class="d-flex align-items-center">
                        <i class="material-icons mr-3">error</i> 
                        <span v-html="description"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 px-0">
                <button class="float-right btn btn-primary ml-2" :disabled="!canConfirm" @click="confirm" v-bind:class="{'shaking' : canConfirm}">Confirmar Medidas</button>
                <button class="float-right btn btn-secondary" :disabled="!confirmed" @click="edit">Editar Medidas</button>
            </div>
        </div>
        
    </template>
</div>
</template>

<script>
export default {
     props:["shared","variation","attribute","options","step"],
    
    data() {
        return {
            skus_ids : this.options.skus,
            row_step : this.stepindex,
            selected_attributes : Object.assign([],this.options.selected_attributes),
            unit : this.shared.data.measures.unit ? this.shared.data.measures.unit : "m",
            width : this.shared.data.measures.width,
            with_limits : null,
            confirmed : false,
            height : this.shared.data.measures.height,
            limits: {},
        }
    },
    mounted() {
        let limits = this.getLimits();
    },
    computed : {
        description() {
            let height = this.height
            let width = this.width
            let unit = this.unit=="cm" ? "Centrimetros" : "Metros"
            
            
            if((height>0.001)&&(width>0.001)) {
                let text = `Está arte terá ${height} <b>${unit}</b> de altura por ${width} <b>${unit}</b> de largura`
                let area = height*width
                if(area-parseInt(area)>0) 
                    area = this.formatNumber(area.toFixed(3))
                else 
                    area = parseInt(area)
                if(this.unit=="cm"){
                    let area_m = area/(100*100)
                    return text+` totalizando uma área de ${area} <b>Centrimetros²</b>`
                }
                return text+` totalizando uma área de ${area} <b>Metros² `
            }
            return null
        },
        canConfirm() {
            return (((this.width>0.001)&&(this.height>0.001))&&(!this.confirmed))
        },
        area() {
            let area = this.width*this.height
            let unit = this.unit
            if(area<0.001)  {
                area*=10000
                unit = "cm"
            }
            area = (area-(parseInt(area))>0) ? this.formatNumber(area.toFixed(3)) : area
            return `<span class="${unit=='cm' ? 'text-danger':''}">${area} ${unit}²</span>`
        },
        scale() {
            return this.getAspectRatio(this.width,this.height,200,50)
        },
        completeUnit() {
            if(this.unit=="cm")
                return "Centimentro"
            return "Metro"
        },
    },
    methods : {
        formatNumber(number)
        {
            return String(number).replace(".",",")
        },
        getLimits() {
            let options   = this.options
            for(let i in options){
                let limits = []
                let option = options[i]
                
                if(option.indexOf("[")>=0 ) {
                    let index1 = option.indexOf("[")
                    let index2 = option.indexOf("]")

                    let text = option.substr(index1, index2-1)
                   
                    limits = text.replace("[","").replace("]","").split(",")
                   
                    for(let i in limits) {
                        limits[i] = (limits[i] == "-" ? 9999999 : Number(limits[i])/100)
                    }
                }
                this.limits[option] = limits ? limits : option
            }
            if(!this.limits) this.limits = {max : [999999999,999999999]}
        },
        edit() {
            this.shared.data.measures.height = null
            this.shared.data.measures.width = null
            this.confirmed = false
            this.$parent.$parent.$parent.ready = false
        },
        confirm() {
            let selected = null
            let error = null
            let product_id = this.shared.data.product_id
           
            for(let i in this.limits) {
               
                if((Number(this.width)<=this.limits[i][1])&&(Number(this.height)<=this.limits[i][0])) {
                    if(product_id == 9 && (Number(this.height) * Number(this.width) < 0.5 )){
                        error = i
                        break;
                    }
                    if(product_id == 48 && (Number(this.height) * Number(this.width) > 0.5)){
                        error = i
                        break;
                    }
                    selected = i
                    break;
                } else  error = i
            }       
            if(!selected)  {
                let area = 0
                let aux = null
                for(let i in this.limits) {
                    let _area = this.limits[i][0]*this.limits[i][1]
                    if(_area>area) {
                        area=_area
                        aux = i
                    }
                }

                let error_title = "<p  class='m-0'>Tamanho máximo excedido</p>"
                if(product_id == 9){
                   
                    error_title = "<p  class='m-0'>Tamanho Mínimo para banner é de 0.5 m². Para este formato selecione o produto Mini Banner</p>"
                }
                
                 if(product_id == 48){
                    error_title = "<p  class='m-0'>Tamanho Máximo para Mini banner é de 0.5 m². Para este formato selecione o produto Banner</p>"
                }
               
                let height = (this.limits[aux][0] == 9999999) ? "" : `Altura : ${this.limits[aux][0]}m. `   
                let width = (this.limits[aux][1] == 9999999)  ? "": `Largura : ${this.limits[aux][1]}m. `
                  
                let message = `<p class='m-0'>` + width + height  
               
                return this.$toastr.error(error_title+message)
             
            }
            
            let obs = (this.shared.data.sku) ? this.shared.data.sku.custom_extra_info.obs : "";
             
            let message = this.description.replace("Está arte terá","Confirma que esta arte deve ter")+" ? </br></br>Obs: " +obs+ `</br>` 
            this.$swal.confirm("Confirmação de Medidas",message,"warning",() => {
                if(this.editing)
                    this.$parent.$parent.$parent.ready = true
                if(selected) {
                    return this.confirmAttr(selected)
                }
                return this.confirmAttr(selected)
            })
        },
        confirmAttr(attr) {
            this.shared.data.measures.height = Number(this.height)
            this.shared.data.measures.unit = this.unit
            this.shared.data.measures.width = Number(this.width)
            if(!this.$parent.$parent.selected_options.includes(attr)) {
                this.$parent.$parent.selected_options.push(attr)
            }
            this.$parent.$parent.actual_step++
            if(!this.confirmed) 
                this.$parent.$parent.load_options()
            this.confirmed = true
            this.$parent.$parent.$parent.ready = true
            
        },
        getAspectRatio(width,height,max,min){
            let resp = {
                width: max,
                height: max
            }
            if ( width < height ) 
                resp.width = (width / height * max)
            else if ( height < width ) 
                resp.height = (height / width * max)

            resp.width = resp.width.toFixed(0)
            resp.height = resp.height.toFixed(0)
            return resp
        }
    }
}
</script>


<style lang="scss" scoped>
    .labelMeasures {
        color: #1B4E01;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 0;
    }
    .inputMeasure {
        padding: 12px 10px;
        height: auto;
        border: 2px solid #1B4E01;
        color: #000;
        font-weight: 700;
        text-align: center;
        font-size: 22px;
        &.inputError {
            border: 2px solid red!important;
        }
        &::placeholder {
            font-size : 15px;
        }
    }
    .model {
        border: 1px solid black;
        min-height : "50px!important";
        max-height : "200px!important";
        max-width : "200px!important";
        min-width : "50px!important";
        background: repeating-linear-gradient(45deg, #1b4e01, #000000 10px, #000000 10px, #677527 20px);
        .width {
            position: absolute;
            top: -25px;
        }
    }
    label.item-attribute {
        margin-top: 20px;
        border: 1px solid black;
        border-radius: 5px;
        width: 100%;
        padding: 24px;
        b {
            font-family:"Roboto",Bold;
            font-size:14px;
            padding-top: 3px;
        }
        &.selected {
            border: 2px solid red;
            -webkit-box-shadow: 0px 0px 29px -17px rgba(94,94,94,1);
            -moz-box-shadow: 0px 0px 29px -17px rgba(94,94,94,1);
            box-shadow: 0px 0px 29px -17px rgba(94,94,94,1);
        }
        input[type=radio]
        {
            display:none;
        }
        .icon {
            color:red;
            font-size:24px;
            margin-right:15px;
            &.invisible {
                color:transparent!important;
            }
        }
    }
</style>


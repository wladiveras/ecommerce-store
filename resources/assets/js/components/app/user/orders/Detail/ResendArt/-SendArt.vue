<template>
    <div>
        <div class="row mb-3">
            <div class="col-md-12">
                <span style="color: #1B4E01">Importe arquivo(s) nos formatos [.jpg, .png, .gif, .cdr, .ai, .eps, .psd ou .pdf]</span>
            </div>
        </div>
        <div v-if="labelBoxes.includes('Arquivo Único')" class="row mb-4 text-center d-flex justify-content-center">
            <unique-upload-box class="card-upload" label="Arquivo Único" uniqueBoxLabel="Arquivo Único" :disable="disableUnique" />
        </div>
        <div v-if="(labelBoxes.length>1)" class="row mb-3 text-center d-flex justify-content-center">
            <template v-for="row in labelBoxes">
                <unique-upload-box  class="card-upload px-2" :label="row" :disable="disableMultiple" />
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: ['order','sku','componentInfo'],
    data() {
        return {
            disableMultiple : false,
            disableUnique   : false,
            files : [],
        }
    },
    watch : {
        files(val) {
            return this.$emit('input',val)
        }
    },
    computed : {
        labelBoxes() {
            let options = this.sku.data.file.file.map( x => x.label)
            // console.log(options)
            return options
        }
    },
    components: {
        'unique-upload-box' : require('./-UniqueUploadBox.vue').default,
    },
    methods: {
        setReady(val) {
            this.$parent.ready = val
        },
        removeFromFiles(position) {
            this.setReady(false)
            let files = this.files
            files.splice(position,1)
            this.files = files
        },
        addFile(newFile) {
            let files = this.files
            let foundedFile = files.find( x => x.label == newFile.label  )
            if(foundedFile) {
                files.splice(foundedFile,1)
            }
            files.push(newFile)
            this.files = files
            return this.files.length-1
        },
        toggleUnique(val , toggle = true) {
            this.disableUnique  = val
            if(toggle) {
                this.disableMultiple  = !val
            }
            if(this.files.length == this.labelBoxes.length-1) {
                return this.setReady(true)
            }
        },
        toggleMultiple(val , toggle = true) {
            this.disableMultiple  = val
            if(toggle) {
                this.disableUnique  = !val
            }
            if(val) {
                return this.setReady(true)
            }
        }
    }
}
</script>

<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <span>Importe arquivo(s) nos formatos [.jpg, .png, .gif, .cdr, .ai, .eps, .psd ou .pdf]</span>
            </div>
        </div>
        <div class="row mb-4 text-center d-flex justify-content-center flex-wrap">
            <unique-upload-box
                :shared="shared"
                class="card-upload"
                label="Arquivo Único"
                uniqueBoxLabel="Arquivo Único"
                :disable="disableUnique"
            />
        </div>
        <div
            v-if="(labelBoxes.length>1)"
            class="row mb-3 text-center d-flex justify-content-center flex-wrap"
        >
            <template v-for="(row,i) in labelBoxes">
                <unique-upload-box
                    :shared="shared"
                    class="card-upload px-2"
                    :label="row"
                    :disable="disableMultiple"
                    :key="i"
                />
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: ['shared', 'componentInfo'],
    data() {
        return {
            variable: {
                _color_4_0_: "4/0",
                _color_4_1_: "4/1",
                _color_4_4_: "4/4",
                _color_1_0_: "1/0",
                _color_1_1_: "1/1",
                _comunicacao_visual_: "comunicacao visual",
                _impressao_digital_: "impressao digital",
                _impressao_offset_: "impressao offset",
            },
            disableMultiple: false,
            disableUnique: false,
            files: [],
            labelBoxes: [],
        }
    },
    components: {
        'unique-upload-box': require('./-UniqueUploadBox.vue').default,
    },
    mounted() {
        this.load()
    },
    methods: {
        setReady(val) {
            //esse vai cair
            this.$parent.ready = val;
            //esse vai ficar
            this.$parent.fileReady = val;
            if(!val){
                this.$parent.accepted = false;
            }
        },
        removeFromFiles(position) {
            this.setReady(false)
            let files = this.files
            files.splice(position, 1)
            this.files = files
        },
        addFile(newFile) {
            let files = this.files
            let foundedFile = files.find(x => x.label == newFile.label)
            if (foundedFile) {
                files.splice(foundedFile, 1)
            }
            files.push(newFile)
            this.files = files
            return this.files.length - 1
        },
        toggleUnique(val, toggle = true) {
            this.disableUnique = val
            if (toggle) {
                this.disableMultiple = !val
            }
            if (this.files.length == this.labelBoxes.length - 1) {
                return this.setReady(true)
            }
        },
        toggleMultiple(val, toggle = true) {
            this.disableMultiple = val
            if (toggle) {
                this.disableUnique = !val
            }
            if (val) {
                return this.setReady(true)
            }
        },
        load() {
            if (this.componentInfo.segment == this.variable._comunicacao_visual_) {
                return this.labelBoxes = ["Arquivo Único"]
            }
            if (this.componentInfo.segment == this.variable._impressao_digital_) {
                return this.checkMultipleImpressaoDigital()
            }
            if (this.componentInfo.segment == this.variable._impressao_offset_) {
                return this.checkMultipleImpressaoOffset()
            }
        },
        checkMultipleImpressaoDigital() {
            if ([this.variable._color_1_0_, this.variable._color_4_0_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Faca"]
            }
            if ([this.variable._color_1_0_, this.variable._color_4_0_].includes(this.componentInfo.color) &&
                !this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Arquivo Único"]
            }
            if ([this.variable._color_1_1_, this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso", "Faca"]
            }
            if ([this.variable._color_1_1_, this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                !this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso"]
            }
            return this.labelBoxes = ["Arquivo Único"]
        },
        checkMultipleImpressaoOffset() {
            if ([this.variable._color_4_0_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Faca", "Máscara Frente"]
            }
            if ([this.variable._color_4_0_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Faca"]
            }
            if ([this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso", "Máscara Frente", "Máscara Verso"]
            }
            if ([this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso", "Faca"]
            }
            if ([this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                !this.componentInfo.hasCutAndCrease &&
                !this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso"]
            }
            if ([this.variable._color_4_0_, this.variable._color_4_1_, this.variable._color_4_4_].includes(this.componentInfo.color) &&
                !this.componentInfo.hasCutAndCrease &&
                this.componentInfo.hasLocalizedVarnish) {
                return this.labelBoxes = ["Frente", "Verso", "Máscara Frente", "Máscara Verso"]
            }
            return this.labelBoxes = ["Arquivo Único"]
        }
    }
}
</script>

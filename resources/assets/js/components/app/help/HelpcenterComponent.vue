<template>
    <div class="helpcenter" v-loading="loading">
        <div class="d-flex align-items-center justify-content-center mt-6 mx-5 flex-column"
        style="max-width: 1080px; margin: 30px auto 0 !important;">
            <h1 class="title">Central de Ajuda</h1>
            <div class="w-100 d-flex justify-content-center align-items-center">
                <div
                    class="input-group my-4 col-sm-12 input-filter d-flex align-items-center"
                >
                    <input
                        @keyup.enter="makeFilter"
                        type="text"
                        class="form-control"
                        v-model="filter"
                        placeholder="Ex: digite sua dúvida aqui"
                    />
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" @click="makeFilter" style="
    border: none;
    margin-left: -46px;
">
                            <i class="material-icons">search</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex mx-5" style="background: rgb(255 255 255 / 73%);max-width: 1050px;margin-right: auto !important;margin-left: auto !important;border-radius: 10px;">
        <div>
            <div class="container-fluid" style="padding-left: 0;">
                <div class="help-topics-container">
                    <template v-if="categories.length>0">
                        <div
                            class="help-topics"
                            v-for="(c,i) in categories"
                            :key="i"
                        >
                            <i class="material-icons mr-2" style="font-size: 25px; width: 25px; vertical-align: bottom;"></i>
                            <!-- <b>{{c.slug}}</b> -->
                            <a href="#" @click.prevent="startReading('category',c)">
                                <b>{{c.category}}</b>
                            </a>
                            <!-- <a
                                v-for="(i,k) in c.topics"
                                href="#"
                                @click.prevent="startReading('topic',i)"
                                :key="k"
                            >- {{i.name}}</a> -->
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-12 text-center mt-5">
                            <h4>Nenhum resultado encontrado</h4>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div v-if="reading">
            <div class="container-fluid" v-if="reading.type=='category'">
                <!-- <nav aria-label="breadcrumb" style="font-size:18px;">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#" @click.prevent="gotoStart">Início</a>
                        </li>
                        <li class="breadcrumb-item active">{{reading.content.category}}</li>
                    </ol>
                </nav> -->
                <div class="categories">
                    <div class="d-flex flex-column mb-3">
                        <h3 class="subcategories mt-4">
                            <b>{{reading.content.category}}</b>
                        </h3>                        
                        <a
                            v-for="(i,k) in reading.content.topics"
                            href="#"
                            @click.prevent="startReading('topic',i)"
                            :key="k"
                        > - {{i.name}}</a>
                    </div>
                </div>
            </div>

            <div class="container-fluid" v-if="reading.type=='topic'">
                <nav aria-label="breadcrumb" style="font-size:18px;">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item">
                            <a
                                href="#"
                                @click.prevent="startReading('category',reading.content)"
                            >{{reading.content.category}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{reading.content.name}}</li>
                    </ol>
                </nav>
                <div class="content d-flex flex-wrap">
                    <div
                        class="col-md-8 mt-2 col-sm-12 pl-0"
                        v-bind:class="{'col-md-12':readingOtherTopics.length<=0}"
                    >
                        <h4>
                            <b>{{reading.content.name}}</b>
                        </h4>
                        <div v-html="reading.content.content" class="responsive-content"></div>
                    </div>
                    <div
                        class="col-md-4 mt-3 col-sm-12 d-flex justify-content-start flex-column subcategories"
                        v-if="readingOtherTopics.length>0"
                    >
                        <h3 class="subcategories">
                            <b>Demais Tópicos Desta Categoria</b>
                        </h3>
                        <a
                            v-for="(c,i) in readingOtherTopics"
                            href="#"
                            @click.prevent="startReading('topic',c)"
                            :key="i"
                        >- {{c.name}}</a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            reading: null,
            loading: null,
            timerFilter: null,
            filter: null,
            categories: [],
        }
    },
    async created() {
        this.makeFilter()
    },
    watch: {
        filter(val) {
            this.reading = null
            clearTimeout(this.timerFilter)
            this.timerFilter = setTimeout(_ => {
                this.makeFilter()
            }, 1000)
        }
    },
    computed: {
        readingOtherTopics() {
            let category = this.categories.find(x => x.category == this.reading.content.category)
            let topics = category.topics.filter(x => x.name != this.reading.content.name)
            return topics
        },
        defaultRoute() {
            return this.$root.root_url + "/central-de-ajuda"
        }
    },
    methods: {
        gotoStart() {
            this.reading = null
            window.history.pushState("", "", `/central-de-ajuda`)
        },
        startReading(type, content, reloadUrl = true) {
            this.reading = {
                type: type,
                content: type == "category" ? this.categories.find(x => x.category == content.category) : content
            }
            if (reloadUrl) {
                if (type == "category") window.history.pushState("", "", `/central-de-ajuda/${content.category.toLowerCase().replace(' ', '-')}`)
                if (type == "topic") window.history.pushState("", "", `/central-de-ajuda/${content.category.slug()}/${content.slug}`)
            }
        },
        makeFilter() {
            this.loading = true
            this.$http.post(this.defaultRoute + "/search", { filter: this.filter }).then(res => {
                res = res.data
                this.categories = res.data
                this.loading = false
                clearTimeout(this.timerFilter)
                if (this.$attrs.category) this.startReading('category', this.$attrs.category, false)
                if (this.$attrs.topic) this.startReading('topic', this.$attrs.topic, false)
            })
        }
    }
}
</script>
<style lang="scss" scoped>
.help-topics {
    padding: 15px 30px;
    font-size: 0.9rem;
    border: solid 1px #ebebeb;
    a {
        color: #5e7b25;
    }
}
.help-topics:first-child i.material-icons:before {
    content: "person_pin";
    color: #5e7b25;
}
.help-topics:nth-child(2) i.material-icons:before {    
    content: "shopping_cart";
    color: #5e7b25;
}
.help-topics:nth-child(3) i.material-icons:before {    
    content: "cloud_upload";
    color: #5e7b25;
}
.help-topics:nth-child(4) i.material-icons:before {    
    content: "assignment_turned_in";
    color: #5e7b25;
}
.help-topics:nth-child(5) i.material-icons:before {    
    content: "folder_special";
    color: #5e7b25;
}
.help-topics:nth-child(6) i.material-icons:before {    
    content: "description";
    color: #5e7b25;
}
.help-topics:nth-child(7) i.material-icons:before {    
    content: "description";
    color: #5e7b25;
}
.help-topics:nth-child(8) i.material-icons:before {    
    content: "description";
    color: #5e7b25;
}
.help-topics:nth-child(9) i.material-icons:before {    
    content: "grade";
    color: #5e7b25;
}

.helpcenter {
    color: #707070;
    .categories {
        font-size: 40px;
        .subcategories {
            font-size: 30px;
        }
        a {
            color: #707070;
            font-size: 18px;
            margin-bottom: 15px;
            &:hover {
                text-decoration: underline;
            }
        }
    }
    .title {
        color: #707070;
        font-weight: 600;
    }
    .filter {
        margin-top: 20px;
        background-color: #d0e08e;
        .input-filter {
            input {
                height: 60px;
                border-radius: 20px 0px 0px 20px;
                text-align: center;
                font-size: 20px;
                &:active {
                    outline: 0px !important;
                    -webkit-appearance: none !important;
                    box-shadow: none !important;
                }
                &:focus {
                    outline: 0px !important;
                    -webkit-appearance: none !important;
                    box-shadow: none !important;
                }
            }
            .btn {
                background-color: white;
                border: unset;
                border-radius: 0px 20px 20px 0px;
                background-color: white;
                border: unset;
                border-right: 1px solid #ced4da;
                &:hover {
                    color: gray;
                }
                &:focus {
                    outline: 0px !important;
                    -webkit-appearance: none !important;
                    box-shadow: none !important;
                }
            }
            .input-group-append {
                height: 58px;
            }
        }
    }
}
</style>
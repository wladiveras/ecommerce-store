<style scoped>
.orange
{
    color:rgb(255, 154, 0);
}
.green
{
    color: #99D468;
}
.purple
{
    color: #B377D9;
}
.red
{
    color: #ED5564;
}
.blue
{
    color: blue;
}
.card-header-tabs-compras
{
    border: 1px solid #DEE2E6;
}
</style>
<template>
<li role="presentation" class="nav-item">
    <a role="tab" tabindex="-1" href="#" id="__BVID__2___BV_tab_button__" @click="changeTab()"  aria-selected="true" aria-setsize="1" aria-posinset="1" aria-controls="__BVID__1__BV_tab_container_"
        class="h-100 nav-link card-header-tabs-compras pt-3" v-bind:class="{ 'active' : isActive }">
        <div class="row">
            <div class="col-md-2"><i class="material-icons mr-2" v-bind:class="color">fiber_manual_record</i></div>
            <div class="col-md-10">
                {{title}} ({{qty}})
            </div>
        </div>
    </a>
</li>
</template>

<script>
export default {
    props: ["active","color","title","count","url","shared"],
    data() {
        return {}
    },
    computed:
    {
        qty()
        {
            if(this.count)
            {
                return this.count;
            }
            return 0;
        },
        isActive()
        {
            return this.active;
        }
    },
    methods:
    {
        changeTab()
        {
            let location = (this.shared.default_route+"/"+this.url).replace("#","");
            if(location.indexOf("?filter=")>0)
            {
                location = location.substring(0, location.indexOf("?filter="));
            }
            location +="?filter="+this.shared.filter;
            return window.location.href = location;
        }
    }
}
</script>

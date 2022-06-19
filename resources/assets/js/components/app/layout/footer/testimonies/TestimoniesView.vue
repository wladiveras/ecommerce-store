<template>
  <div id="testimonies" class="mt-5 d-none d-lg-block" v-if="chunkedTestimonies.length>0" v-show="$root.showIfFiltering">
    <div class="row justify-content-center flex-column align-items-center">
      <p class="f-32 f-space text-center"><span class="font-weight-light">NOSSOS</span> <span class="font-weight-bold">DEPOIMENTOS</span></p>
      <el-carousel :interval="5000" arrow="never" trigger="click" indicator-position="outside" class="col-sm-12">
        <el-carousel-item v-for="(testimonies,i) in chunkedTestimonies" :key="i">
          <div class="d-flex h-100 justify-content-center">
            <div class="col-md-4 col-sm-12 h-100" v-for="testimony in testimonies">
              <div class="card shadow-center h-100">
                <div class="px-4 pt-3">
                  <div class="d-flex justify-content-start align-items-center flex-row">
                    <img-loader style="width : 50px;height : 50px;"  :height="50" :width="50" class="img-rounded ml-0 mr-3" v-if="testimony.files.length>0" :url="testimony.files[0].url"/>
                    <img-loader style="width : 50px;height : 50px;"  :height="50" :width="50" class="img-rounded ml-0 mr-3" v-else url="/assets/images/avatar.png"/>
                    <b>{{testimony.name}}</b>
                  </div>
                </div>
                <div class="card-body px-4 pt-2 d-flex flex-column">
                  <component-rate size="medium" class="mt-2" :val="testimony.rate" readonly></component-rate>
                  <div class="h-100 d-flex align-items-center" v-html="testimony.text"></div>
                </div>
              </div>
            </div>
          </div>
        </el-carousel-item>
      </el-carousel>
    </div>
  </div>
</template>
<script>
export default {
  props:{
    _values:{
      default(){
        return {
            _testimonies : null
        }
      }
    }
  },
  computed:{
    chunkedTestimonies(){
      return _.chunk(this._values._testimonies,3);
    }
  },
  mounted() {
      this.getTestimonies();
  },
  methods:
  {
      getTestimonies()
      {
          this.$http.post(this.$root.root_url+"/api/testimonies/get",{}).then(response =>
          {
              response = response.data ? response.data : [];
              if(response.data.length>0) this._values._testimonies = response.data
          });
      }
  }
}
</script>
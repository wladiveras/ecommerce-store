<template>
  <div class="col-md-3">
    <div class="card" v-loading="loading">
      <div class="card-body card-body pb-1 pt-1">
        <div class="row row-custom">
          <div class="col-2 p-0">
            <button class="btn-custom minus btn-block" :disabled="minus_disabled" @click="minus">
              <b>-</b>
            </button>
          </div>
          <div
            class="col-8 justify-content-center d-flex align-items-center col-title"
            ref="content"
          >
            <transition name="fade">
              <b v-if="!loading">{{item.name}} ({{qty}})</b>
            </transition>
          </div>
          <div class="col-2 p-0">
            <button class="btn-custom plus btn-block" :disabled="plus_disabled" @click="plus">
              <b>+</b>
            </button>
          </div>
        </div>
      </div>
      <small class="text-danger mt-0" v-if="plus_disabled&&max>=1">
        Limitado a {{max}} unidade<span v-if="max>1">s</span>
      </small>
    </div>
  </div>
</template>
<script>
export default {
  props: ["shared", "item", "data"],
  data() {
    return {
      qty: 0,
      min: 0,
      _max: -1,
      minus_disabled: true,
      plus_disabled: false,
      loading: false
    };
  },
  computed: {
    max() {
      if (["canteamento"].includes(this.item.name.toLowerCase())) {
        return 4;
      }
      return this.$data._max;
    },
    has_rules() {
      return this.shared.data.skus[0].sku.amount_rule.match(/[^\d]/);
    },
    cantAddMoreFinishes() {
      return (
        this.qty == this.max
      );
    }
  },
  watch: {
    qty(value) {
      if (!this.shared.data.finishes[this.item.id])
        this.shared.data.finishes[this.item.id] = {
          qty: this.qty,
          price: 0,
          ref: this.item.finish_ref_id,
          name: this.item.name.toLowerCase()
        };
      else this.shared.data.finishes[this.item.id].qty = value
    },
    "shared.data.chosenFinish"(n) {
      if (this.cantAddMoreFinishes) {
        this.plus_disabled = true;
      } else {
        this.plus_disabled = false;
      }
    }
  },
  methods: {
    get_qty() {
      let qty = 0;
      if (!this.shared.data.skus) return qty;
      let skus = this.shared.data.skus;
      for (let i = 0; i < skus.length; i++) {
        if(this.has_rules){
          qty+=Number(skus[i].qty);
        }else  {
          let attr = Number(skus[i].attribute) || Number(skus[i].fvalue)
          qty+=(attr *Number(skus[i].qty));
        }
      }
      return qty;
    },
    calc_finishes() {
      this.loading = true;
      let qty_sku = this.get_qty();
      let sku = {
        id: null,
        price: 0
      };
      let skus = this.shared.data.skus;
      for (let row in skus) {
        if (skus[row].sku.reseller_price > sku.price) {
          sku.id = skus[row].sku.id;
          sku.price = skus[row].sku.reseller_price;
        }
      }
      let data = {
        finishes: this.shared.data.finishes,
        finish_ref_id: this.item.finish_ref_id,
        department: this.data.product.department,
        sku_id: sku.id,
        qty_sku: qty_sku,
        qty_finish: this.qty
      };
      Object.assign(this.shared.data.requests, { [this.item.id]: data });
      this.$http
        .post(
          this.$root.root_url + "/api/produto_config/get_calc_finishes",
          data
        )
        .then(response => {
          response = response.data;
          if (!response.success) {
            this.loading = false;
            return this.$toastr.error(response.message);
          }
          this.$data._max = response.data.maxQuantity
            ? response.data.maxQuantity
            : -1;
          this.loading = false;
          if (this.qty == this.max) this.plus_disabled = true;

          if (qty_sku == 0) return this.set_finish_price(0, 0);
          else
            this.set_finish_price(
              response.data.additionalPrice,
              response.data.additionalTime,
              response.data.ref ? response.data.ref : null
            );
        })
        .catch(() => {
          this.loading = false;
        });
    },
    set_finish_price(price, time, ref) {
      this.shared.data.finishes[this.item.id].price = price;
      this.shared.data.finishes[this.item.id].time = time;
      if(ref)
      this.shared.data.finishes[this.item.id].ref = ref;
      this.force_reative();
    },
    force_reative() {
        if (this.shared.step.position < 10) {
            this.shared.data.skus = JSON.parse(JSON.stringify(this.shared.data.skus))
        }
        this.shared.data.finishes = JSON.parse(JSON.stringify(this.shared.data.finishes));
    },
    plus() {
      if (this.cantAddMoreFinishes) return;

      this.qty++;
      this.minus_disabled = false;
      this.shared.data.chosenFinish = this.item.finish_ref_id;

      if (this.qty == this.max) this.plus_disabled = true;
      this.calc_finishes();
    },
    minus() {
      if (this.qty == this.min) this.minus_disabled = true;
      this.qty--
      if(this.qty <= 0){
          this.shared.data.chosenFinish = ""
      }
      this.plus_disabled = false;
      if (this.qty == this.min) this.minus_disabled = true;
      this.calc_finishes();
    }
  }
};
</script>
<style scoped>
.card {
  border-radius: 0;
  border: none;
}
.card-body .btn-custom {
  padding: 10px;
  border: none;
}
.btn-custom {
  font-size: 20px;
}

.btn-custom.minus {
  background-color: #1b4e01;
  color: #d6ef63;
  opacity: 0.5;
}
.btn-custom.plus:disabled {
  background-color: #d8d8d8;
  color: black;
  cursor: not-allowed;
}
.btn-custom.minus:disabled {
  background-color: #d8d8d8;
  color: black;
  cursor: not-allowed;
}
.btn-custom.minus:hover:enabled {
  opacity: 1;
  transition: 0.5s;
}
.btn-custom.plus {
  background-color: #d6ef63;
  color: #1b4e01;
  opacity: 0.5;
}
.btn-custom.plus:hover:enabled {
  opacity: 1;
  transition: 0.5s;
}
.row-custom {
  margin-right: -19px;
  margin-left: -19px;
}
.col-title {
  border: 1px solid #e8e9ea;
}
</style>

<template>
  <div class="summary-wrapper">
    <transition name="fade">
      <div>
        <div
          v-if="can_continue"
          style="
            position: fixed;
            height: 100%;
            width: 100%;
            left: 0;
            top: 0;
            background: #ffffffa6;
            z-index: 999;
          "
        >
          <button
            class="btn-continue btn-block btn-finish-primary mb-3"
            id="btn_confirm"
            @click="submit"
            v-bind:class="{ shaking: can_continue && !is_showing_modal }"
            v-if="can_continue"
          >
            FINALIZAR PEDIDO
          </button>
          <div v-if="data.product.customizable == '0'">
            <a
              href="#"
              id="reset-options"
              v-if="shared.step.position > 9"
              @click.prevent="edit"
              style="font-weight: bold; text-decoration: underline"
              class="d-flex align-items-center justify-content-center"
            >
              <i class="material-icons mr-2">edit</i> Editar pedido
            </a>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="card" id="sumary_section">
              <div class="card-header finished">
                <div class="row d-flex align-items-center">
                  <div class="col-md-12">
                    <b class="float-left">RESUMO</b>
                    <a
                      href="#"
                      id="reset-options"
                      v-if="shared.step.position > 9"
                      @click.prevent="edit"
                      class="float-right d-flex align-items-center"
                    >
                      <i class="material-icons mr-2">edit</i> Editar pedido
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex flex-wrap flex-column">
                  <div class="d-flex row_content justify-content-between">
                    <div>
                      <b>Produto : </b>{{ shared.step.product.name }} (
                      {{ getDepartment(shared.step.product.department) }} )
                    </div>
                  </div>

                  <div
                    class="d-flex row_content"
                    v-for="(option, index) in shared.data.options"
                    v-if="!(index == 'Formato' && hasMeasures)"
                  >
                    <div>
                      <b>{{ index }} : </b> {{ shared.data.options[index] }}
                    </div>
                  </div>

                  <div class="d-flex row_content" v-if="hasMeasures">
                    <div>
                      <b>Medidas : </b
                      >{{
                        `${formatedArea} (${shared.data.measures.width}${shared.data.measures.unit} x ${shared.data.measures.height}${shared.data.measures.unit})`
                      }}
                    </div>
                  </div>

                  <div
                    class="d-flex row_content justify-content-between"
                    v-if="
                      shared.step.has_cover &&
                      shared.step.covers &&
                      shared.data.extra.cover
                    "
                  >
                    <div><b>Capa : </b></div>
                    <div>{{ shared.data.extra.cover }}</div>
                  </div>

                  <template v-if="qty > 0">
                    <div class="d-flex row_content justify-content-between">
                      <div>
                        <b v-if="hasMeasures">Preço Por M² : </b
                        ><b v-else>Preço Unitário : </b>
                      </div>
                      <div>{{ skusPrice.currency() }}</div>
                    </div>
                    <div class="d-flex row_content justify-content-between">
                      <div>
                        <b>Quantidade : </b>{{ qty }} unidade<template
                          v-if="qty > 1"
                          >s</template
                        >
                      </div>
                      <div v-if="shared.step.has_pricing_role">
                        {{ subtotal.currency() }}
                      </div>
                    </div>
                    <div class="d-flex row_content" v-if="hasMeasures && qty">
                      <div>
                        <b>Area total : </b>{{ formatedArea }} x
                        {{ qty }} unidade<span v-if="qty > 1">s</span> =
                        {{ `${(measuresArea * qty).toFixed(3)}m²` }}
                      </div>
                    </div>
                  </template>

                  <template
                    v-if="Object.keys(shared.data.additional).length > 0"
                  >
                    <div
                      class="d-flex row_content justify-content-between"
                      v-for="add in shared.data.additional"
                      v-if="add"
                    >
                      <div><b>Checagem : </b>{{ get_adicional_name(add) }}</div>
                      <div>{{ get_adicional_price(add) }}</div>
                    </div>
                  </template>

                  <template
                    v-if="shared.data.finishes && shared.step.is_tshirt"
                  >
                    <div
                      class="d-flex row_content justify-content-between"
                      v-for="size in shared.data.sizes"
                    >
                      <div>
                        ><b>Tamanho {{ size.label }} : </b>
                        {{ size.qty }} unidade<template v-if="size.qty > 1"
                          >s</template
                        >
                      </div>
                    </div>
                  </template>

                  <template v-if="shared.step.has_finishes">
                    <template v-for="(option, index) in shared.data.finishes">
                      <div
                        class="d-flex row_content justify-content-between"
                        v-if="option"
                      >
                        <div>
                          <b>{{ get_finish_name(index) }} : </b>
                          {{ option.qty }} unidade<template
                            v-if="option.qty > 1"
                            >s</template
                          >
                        </div>
                        <div v-if="option.price">
                          +{{ option.price.currency() }}
                        </div>
                      </div>
                      <template
                        v-if="option && option.ref == 4 && option.qty > 0"
                      >
                        <div
                          class="d-flex row_content justify-content-between"
                          v-if="option"
                        >
                          <div><b>Faca : </b> 1 unidade</div>
                          <div v-if="option.price">+ R$ 150,00</div>
                        </div>
                      </template>
                    </template>
                    <template v-if="additionalTime > 0">
                      <div
                        class="d-flex row_content justify-content-between"
                        v-if="option"
                      >
                        <div>
                          <b>Tempo Adicional : </b>+{{
                            additionalTime
                          }}
                          Dia<template v-if="additionalTime > 1">s</template>
                        </div>
                      </div>
                    </template>
                  </template>
                </div>
              </div>
              <div class="card-footer" v-if="shared.data.skus">
                <div class="row">
                  <div class="col-md-6 text-left"><b>Total</b></div>
                  <div class="col-md-6 text-right">
                    <b>{{ price.currency() }}</b>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
    <template-card :files="data.files" />
  </div>
</template>

<script>
export default {
  props: ["shared", "data"],
  data() {
    return {
      loading: null,
      is_showing_modal: false,
    };
  },
  components: {
    "template-card": require("./-FileTemplate.vue").default,
  },
  computed: {
    hasMeasures() {
      return (
        this.shared.data.measures.width && this.shared.data.measures.height
      );
    },
    formatedArea() {
      let value = null;
      let area = this.measuresArea;
      let unit = this.shared.data.measures.unit;
      let int_area = parseInt(area);
      if (area - int_area > 0) {
        area = area.toFixed(3);
      }
      return `${area}m²`;
    },
    measuresArea() {
      if (!this.hasMeasures) return 0;
      let unit = this.shared.data.measures.unit;
      let width = Number(this.shared.data.measures.width);
      let height = Number(this.shared.data.measures.height);
      if (unit == "cm") {
        width = width / 100;
        height = height / 100;
      }
      let full_area = Number(width * height);
      return full_area;
    },
    refIdList() {
      return _.map(this.shared.data.skus, (e) => e.sku.ref_id);
    },
    can_continue() {
      console.log(this.shared.step.position);
      return this.shared.step.position > 9;
    },
    skusPrice() {
      let price = 0;
      if (!this.shared.data.skus) return price;
      let skus = this.shared.data.skus;
      for (let i = 0; i < skus.length; i++) {
        if (skus[i].sku.reseller_price) {
          let aux = Number(skus[i].sku.reseller_price);
          price += aux;
        } else {
          let aux = Number(skus[i].sku.price);
          price += aux;
        }
      }
      this.shared.data.amount = price;
      return price;
    },
    additionalTime() {
      let time = 0;
      let finishes = this.shared.data.finishes;
      for (let row in finishes) {
        if (finishes[row].qty <= 0) {
          this.shared.data.finishes[row].time = 0;
          this.shared.data.finishes[row].price = 0;
          this.shared.data.finishes[row].qty = 0;
        }
        time = finishes[row].time > time ? finishes[row].time : time;
      }
      return time;
    },
    subtotal() {
      let price = 0;
      if (!this.shared.data.skus) return price;
      let skus = this.shared.data.skus;
      for (let i = 0; i < skus.length; i++) {
        let aux = Number(skus[i].sku.reseller_price) * Number(skus[i].qty);
        price += aux;
      }
      this.shared.data.amount = price;
      return price;
    },
    price() {
      let price = 0;
      if (!this.shared.data.skus) return price;
      let skus = this.shared.data.skus;
      let area = this.measuresArea;
      let TotalArea = area * this.qty;
      // console.log(area,this.qty)
      if (TotalArea > 0) return this.pricaWithMeasures;
      for (let i = 0; i < skus.length; i++) {
        let sku_price = Number(
          skus[i].sku.reseller_price
            ? skus[i].sku.reseller_price
            : skus[i].sku.price
        );
        // console.log(sku_price,TotalArea)
        if (TotalArea < 1) {
          price += sku_price * Number(skus[i].qty);
        } else {
          price += sku_price * TotalArea;
        }
      }
      this.shared.data.amount = price;
      return this.sum_finishes(this.sum_additional(price));
    },
    pricaWithMeasures() {
      let price = 0;
      if (!this.shared.data.skus) return price;
      let skus = this.shared.data.skus;
      let area = 0;
      area = this.measuresArea;
      let TotalArea = area * this.qty;
      for (let i = 0; i < skus.length; i++) {
        let sku_price = Number(
          skus[i].sku.reseller_price
            ? skus[i].sku.reseller_price
            : skus[i].sku.price
        );
        if (TotalArea > 1) {
          price += sku_price * TotalArea;
        } else {
          price += sku_price;
        }
      }
      this.shared.data.amount = price;
      return this.sum_finishes(this.sum_additional(price));
    },
    qty() {
      let qty = 0;
      if (!this.shared.data.skus) return qty;
      let skus = this.shared.data.skus;
      if (Number.isInteger(parseInt(skus[0].rule))) {
        for (let sku in skus) {
          qty += Number(skus[sku].qty) * Number(skus[sku].rule);
        }
      } else {
        for (let sku in skus) {
          qty += Number(skus[sku].qty);
        }
      }
      return qty;
    },
  },
  methods: {
    getDepartment(department) {
      switch (department) {
        case "impressao digital":
          return "Impressão Digital";
          break;
        case "comunicacao visual":
          return "Comunicação Visual";
          break;
        case "Impressão Digital":
          return "Impressão Digital";
          break;
        default:
          return department;
          break;
      }
    },
    get_finish_name(id) {
      return this.shared.step.finishes.find((x) => x.id == id).name;
    },
    get_adicional_name(add) {
      let configs = this.data.configs;
      for (let config in configs) {
        let attr = configs[config].attr.find((x) => x.id == add);
        if (attr) {
          return attr.name;
        }
      }
    },
    get_adicional_price(add) {
      let configs = this.data.configs;
      for (let config in configs) {
        let attr = configs[config].attr.find((x) => x.id == add);

        if (attr) {
          let price = attr.price;
          if (price > 0) return "+" + price.currency();
          return "Grátis";
        }
      }
    },
    sum_finishes(price) {
      let finishes = this.shared.data.finishes;
      let has_corte_vinco = false;
      for (var finish in finishes) {
        let value = finishes[finish];
        price += Number(value.price);
        if (finishes[finish].ref == 4 && value.price > 0) {
          has_corte_vinco = true;
        }
      }
      if (has_corte_vinco) {
        price += 150;
      }
      return price;
    },
    sum_additional(price) {
      let additionals = this.shared.data.additional;
      let add_price = 0;
      for (let config_id in additionals) {
        let configs = this.data.configs;
        for (let config in configs) {
          let attr = configs[config].attr.find(
            (x) => x.id == additionals[config_id]
          );
          if (attr) {
            add_price += attr.price;
          }
        }
      }
      return price + add_price;
    },
    edit() {
      this.shared.step.position--;
    },
    submit(ask = true) {
      if (ask) {
        this.is_showing_modal = true;
        return this.$parent.showModalConfirm();
      } else {
        switch (this.shared.step.f_type) {
          case "FINISH_AND_GO_TO_CART":
            this.finish_and_go_to("cart");
            break;
          case "FINISH_AND_CONTINUE_BUYING":
            this.finish_and_go_to("products");
            break;
          case "FINISH_AND_CREATE_A_CLONE":
            this.finish_and_go_to("clone");
            break;
          default:
            this.finish_and_go_to("cart");
            break;
        }
      }
    },
    async finish_and_go_to(route_back) {
      this.loading = this.$loading();
      if (this.data.needs_measures) {
        this.shared.data.options.Formato = null;
      }
      let data = Object.assign(this.shared.data, {});
      //   delete data.sku
      //   delete data.sku_ids
      data.order_qty = this.qty;
      data["route_back"] = route_back;
      console.log("submit_order", data);
      console.log("submit_order2", this.data);
      if (this.data.product.customizable == "1") {
        let quantidade = localStorage.getItem("qty_item");
        data.upload = null;
        data.order_qty = parseFloat(quantidade);
        data.requests = [];
        data.chosenFinish = [];
        data.comment = "Cria Fácil";
        (data.art_creation_info = {
          accepted_termos: false,
          personal_info: [],
          company_info: [],
        }),
          (data.department = "comunicacao visual");
        (data.product_id = 50),
          (data.additional = {
            1: 1,
          }),
          (data.chosen = "UP");
        // data.rule = "1";
        // data.price = this.data.product.base_price;
        // data.fvalue = "50";
        data.sku_ids = undefined;
        data.sizes = null;
        data.amount = "a partir de 1";
        data.finishes = {};
        data.extra = {
          cover: null,
        };
        data.finishes = {};
        data.upload = {
          file: [
            {
              thumbnail: "nao-se-aplica",
              file: {
                name: "nao-se-aplica",
                dir: "nao-se-aplica",
                ref: "62a34af3b6936",
                slug: "nao-se-aplica",
                extension: "jpg",
                type: "image",
                private: 1,
                metadata: {
                  width: 0,
                  height: 0,
                  all: {
                    0: 0,
                    1: 0,
                    2: 0,
                    3: 'width="0" height="0"',
                    bits: 8,
                    channels: 3,
                    mime: "image/jpeg",
                  },
                },
                updated_at: "2022-06-10 10:45:23",
                created_at: "2022-06-10 10:45:23",
                id: 0,
                url: "nao-se-aplica",
                raw_dir: "nao-se-aplica",
                raw_url: "nao-se-aplica",
                raw_thumbnail: "nao-se-aplica",
                thumbnail: "nao-se-aplica",
                width: 0,
                height: 0,
              },
              label: "Arquivo Único",
            },
          ],
          timing: "now",
        };
        data.options = {
          Papel: "Offset",
          Gramatura: "75",
          Cor: "4/0",
          Formato: "15x21 cm",
          "Acabamento Padrão": "Com Lombada Quadrada (140 folhas)",
        };

        localStorage.removeItem("qty_item");
        await this.load_options();

        data.sku.finishes = [];

        data.skus = [{ 
          "rule": "1", 
          "amount": "a partir de 1", 
          "price": this.data.product.base_price,
          "fvalue": "50",
          "sku": data.sku,
          "qty": parseFloat(quantidade)
        }]
        
      }
      this.$http
        .post(this.$root.root_url + "/api/produto_config/submit_order", data)
        .then((response) => {
          response = response.data;
          localStorage.removeItem("shared");
          if (!response.success) {
            return this.$toastr.error(response.message);
          }
          if (route_back == "clone") return this.setCloneOptions();
          else return (window.location.href = response.data[route_back]);
        })
        .catch((errors) => {
          this.loading.close();
          let data = errors.response.data.errors;
          let message = "<ul>";
          for (let i in data) {
            message += `<li>${data[i]}</li>`;
          }
          message += "</ul>";
          return this.$toastr.error(message);
        });
    },
    load_options() {
      this.$http
        .post(this.$root.root_url + "/api/menu/get_menu_options", {})
        .then((res) => {
          res = res.data;
          this.options = res.options;
          this.loaded = true;
          if (this.options.length > 0) this.select_option(this.options[0]);
        })
        .catch((er) => {
          this.loaded = true;
          console.log(er);
        });
    },
    select_option(op) {
      if (!op) return;
      this.options.map((x) => (x.actual = op.value == x.value ? true : false));
      this.$set(op, "actual", true);
      this.selected_option = op;
      this.selected_option.values = op.values;
    },
    setCloneOptions() {
      this.$parent.back_step(9);
      this.loading.close();
      let name = this.shared.data.ref_name;
      this.shared.data.ref_name = null;
      this.shared.data.comment = null;
      let message =
        "<ul>" +
        `<li>Pedido <b>${name}</b> adicionado ao carrinho com sucesso, acesse o carrinho para visualiza-lo !!</li>` +
        "<li>Se for necessário apenas altere as informações e confirme para gerar um novo pedido semelhante ao anterior</li>" +
        "</ul>";
      let qty_cart = $("#qty_cart").html();
      qty_cart = parseInt(!qty_cart ? 0 : qty_cart) + 1;
      if (this.data.product.customizable == "1") {
        let data = Object.assign(this.shared.data, {});
        qty_cart = data.order_qty;
      }
      $("#qty_cart").html(qty_cart);
      this.$toastr.success(message, null, {
        positionClass: "toast-top-full-width",
      });
    },
  },
};
</script>
<style scoped>
.summary-wrapper {
  position: sticky;
  top: 15px;
  padding-bottom: 16px;
}

.btn-finish-primary {
  background-color: #1594a2;
  color: #fff;
  border: none;
  padding: 10px;
}

.btn-finish-primary:hover {
  background-color: #0a575f;
  color: #fff;
  transition: 0.5s;
}

#btn_confirm {
  width: 300px;
  height: 100px;
  border-radius: 10px;
  margin: 100px auto 0;
}

.row_content {
  margin-bottom: 15px;
}
</style>

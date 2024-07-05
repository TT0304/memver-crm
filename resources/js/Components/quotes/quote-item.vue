<template>
  <tr class="quote-item">
    <td>
      <div class="form-group">
        <VField
          type="text"
          :name="inputName + '.product_id_label'"
          class="form-control"
          v-model="product.name"
          autocomplete="off"
          :rules="'required'"
          :label="$t('quotes.name')"
          :class="errors[inputName + '.product_id_label'] ? 'is-invalid' : ''"
          @keyup="search"
          :placeholder="$t('quotes.search')"
        />

        <VField
          type="hidden"
          :name="inputName + '.product_id'"
          v-model="product.product_id"
          :rules="'required'"
          :label="$t('quotes.name')"
        />

        <div class="lookup-results" v-if="state == ''">
          <ul>
            <li
              v-for="(product, index) in products"
              @click="addProduct(product)"
              :key="index"
            >
              <span>@{{ product.name }}</span>
            </li>

            <li v-if="!products.length && product['name'].length && !is_searching">
              <span>{{ $t("common.no-result-found") }}</span>
            </li>
          </ul>
        </div>

        <!-- <i class="icon loader-active-icon" v-if="is_searching"></i> -->
        <BFormInvalidFeedback
          class="d-flex"
          v-if="errors[inputName + '.product_id_label']"
          >{{ errors[inputName + ".product_id_label"] }}</BFormInvalidFeedback
        >
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField
          type="text"
          :name="inputName + '.quantity'"
          class="form-control"
          v-model="product.quantity"
          :rules="'required|decimal:4'"
          :label="$t('quotes.quantity')"
          :class="[errors[inputName + '.quantity'] ? 'is-invalid' : '']"
        />
        <BFormInvalidFeedback class="d-flex" v-if="errors[inputName + '.quantity']">{{
          errors[inputName + ".quantity"]
        }}</BFormInvalidFeedback>
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField
          type="text"
          :name="inputName + '.price'"
          class="form-control"
          v-model="product.price"
          :rules="'required|decimal:4'"
          :label="$t('quotes.price')"
          :class="[errors[inputName + '.price'] ? 'is-invalid' : '']"
        />
        <BFormInvalidFeedback class="d-flex" v-if="errors[inputName + '.price']">{{
          errors[inputName + ".price"]
        }}</BFormInvalidFeedback>
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField :name="inputName + '.total'" v-slot="{ field }">
          <BFormInput
            class="form-control"
            v-bind="field"
            readonly
            :value="isNaN(product.price * product.quantity) ? 0 : product.price * product.quantity"
            :class="[errors[inputName + '.price'] ? 'is-invalid' : '']"
          >
          </BFormInput>
        </VField>
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField
          type="text"
          :name="inputName + '.discount_amount'"
          class="form-control"
          v-model="product.discount_amount"
          :rules="'required|decimal:4'"
          :label="$t('quotes.discount')"
          :class="[errors[inputName + '.discount_amount'] ? 'is-invalid' : '']"
        />
        <BFormInvalidFeedback
          class="d-flex"
          v-if="errors[inputName + '.discount_amount']"
          >{{ errors[inputName + ".discount_amount"] }}</BFormInvalidFeedback
        >
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField
          type="text"
          :name="inputName + '.tax_amount'"
          class="form-control"
          v-model="product.tax_amount"
          :rules="'required|decimal:4'"
          :label="$t('quotes.tax')"
          :class="[errors[inputName + '.tax_amount'] ? 'is-invalid' : '']"
        />

        <BFormInvalidFeedback class="d-flex" v-if="errors[inputName + '.tax_amount']">{{
          errors[inputName + ".tax_amount"]
        }}</BFormInvalidFeedback>
      </div>
    </td>

    <td>
      <div class="form-group">
        <VField :name="inputName + '.total_price'" v-slot="{ field }">
          <BFormInput
            type="text"
            class="form-control"
            v-bind="field"
            :name="inputName + '.total_price'"
            :value="
              isNaN(
                parseFloat(product.price * product.quantity) +
                  parseFloat(product.tax_amount) -
                  parseFloat(product.discount_amount)
              )
                ? 0
                : parseFloat(product.price * product.quantity) +
                  parseFloat(product.tax_amount) -
                  parseFloat(product.discount_amount)
            "
            :class="[errors[inputName + '.price'] ? 'is-invalid' : '']"
            readonly
          />
        </VField>
      </div>
    </td>

    <td class="actions" style="padding-top: 10px">
      <i
        class="ri-delete-bin-5-line"
        @click="removeProduct"
        v-if="this.$parent.products.length > 1"
      ></i>
    </td>
  </tr>
</template>

<script>
import axios from "axios";
export default {
  components: {},
  props: ["index", "product", "errors"],

  data: function () {
    return {
      is_searching: false,
      state: this.product["product_id"] ? "old" : "",
      products: [],
    };
  },

  computed: {
    inputName: function () {
      if (this.product.id) {
        return "items." + this.product.id;
      }
      return "items.item_" + this.index;
    },
  },

  methods: {
    search() {
      setTimeout(function () {
        console.log(this.product);
        this.state = "";
        this.product["product_id"] = null;
        this.is_searching = true;
        if (this.product["name"].length < 2) {
          this.products = [];
          this.is_searching = false;
          return;
        }

        var self = this;

        axios
          .get(route("products.search"), {
            params: { query: this.product["name"] },
          })
          .then(function (response) {
            self.$parent.products.forEach(function (addedProduct) {
              response.data.forEach(function (product, index) {
                if (product.id == addedProduct.product_id) {
                  response.data.splice(index, 1);
                }
              });
            });

            self.products = response.data;

            self.is_searching = false;
          })
          .catch(function (error) {
            self.is_searching = false;
          });
      }, 500);
    },

    addProduct: function (result) {
      this.state = "old";

      this.product.product_id = result.id;
      this.product.name = result.name;
      this.product.price = result.price;
      this.product.quantity = result.quantity;
      this.product.discount_amount = 0;
      this.product.tax_amount = 0;
    },

    removeProduct: function () {
      this.$emit("onRemoveProduct", this.product);
    },
  },
};
</script>

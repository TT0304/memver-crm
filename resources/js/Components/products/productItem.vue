<template>
  <BRow class="g-3 border-bottom pb-3 mb-3">
    <BCol lg="12">
      <div class="mb-1">
        <label :for="inputName + '.product_id'" class="form-label required">{{
          $t("leads.item")
        }}</label>
        <VField type="hidden" :name="inputName + '.name'" v-model="product['name']" />
        <VField
          type="hidden"
          :name="inputName + '.product_id'"
          v-model="product.product_id"
          rules="required"
          :label="$t('leads.item')"
          v-slot="{ errors }"
        >
          <VField
            :label="$t('leads.item')"
            v-model="product['name']"
            :name="inputName + '.product_idID_Label'"
            rules="required"
          >
            <BFormInput
              type="text"
              class="form-control"
              :name="inputName + '.product_idID_Label'"
              v-model="product['name']"
              @keyup="search"
              :placeholder="$t('common.start-typing')"
              :class="[errors.length > 0 ? 'is-invalid' : '']"
            />
          </VField>
          <div class="lookup-results" v-if="state == ''">
            <ul>
              <li
                v-for="(product, index) in products"
                :key="index"
                @click="addProduct(product)"
              >
                <span>@{{ product.name }}</span>
              </li>

              <li
                v-if="
                  !products.length &&
                  product['name'] != null &&
                  product['name'].length &&
                  !is_searching
                "
              >
                <span>{{ $t("common.no-result-found") }}</span>
              </li>
            </ul>
          </div>
          <i class="icon loader-active-icon" v-if="is_searching"></i>
          <BFormInvalidFeedback v-if="errors.length">{{
            errors[0]
          }}</BFormInvalidFeedback>
        </VField>
      </div>
    </BCol>
    <BCol lg="4">
      <div class="mb-1">
        <label :for="inputName + '.price'" class="form-label required">{{
          $t("leads.price")
        }}</label>
        <VField
          type="text"
          :name="inputName + '.price'"
          class="form-control"
          v-model="product.price"
          rules="required"
          :label="$t('leads.price')"
          v-slot="{ errors }"
        >
          <BFormInput
            type="text"
            :name="inputName + '.price'"
            class="form-control"
            v-model="product.price"
            :class="[errors.length > 0 ? 'is-invalid' : '']"
          />
          <BFormInvalidFeedback v-if="errors.length">{{
            errors[0]
          }}</BFormInvalidFeedback>
        </VField>
      </div>
    </BCol>
    <BCol lg="4">
      <div class="mb-1">
        <label :for="inputName + '.quantity'" class="form-label required">{{
          $t("leads.quantity")
        }}</label>
        <VField
          type="text"
          :name="inputName + '.quantity'"
          v-model="product.quantity"
          rules="required"
          :label="$t('leads.quantity')"
          v-slot="{ errors }"
        >
          <BFormInput
            type="text"
            :name="inputName + '.quantity'"
            class="form-control"
            v-model="product.quantity"
            :class="[errors.length > 0 ? 'is-invalid' : '']"
          />
          <BFormInvalidFeedback v-if="errors.length">{{
            errors[0]
          }}</BFormInvalidFeedback>
        </VField>
      </div>
    </BCol>
    <BCol lg="4">
      <label :for="inputName + '.amount'" class="form-label required">{{
        $t("leads.amount")
      }}</label>

      <VField
        type="text"
        :name="inputName + '.amount'"
        rules="required"
        :label="$t('leads.amount')"
        :value="product.price * product.quantity"
        v-slot="{ errors }"
      >
        <div class="mb-1 d-flex align-items-start">
          <BFormInput
            type="text"
            :name="inputName + '.amount'"
            class="form-control"
            :value="product.price * product.quantity"
            :class="[errors.length > 0 ? 'is-invalid' : '']"
            disabled
          />
          <BButton
            variant="danger"
            class="btn-icon waves-effect waves-light ms-2 align-self-center"
            @click="removeProduct()"
            ><i class="ri-delete-bin-5-line"></i
          ></BButton>
        </div>
        <BFormInvalidFeedback class="d-flex" v-if="errors.length">{{
          errors[0]
        }}</BFormInvalidFeedback>
      </VField>
    </BCol>
  </BRow>
</template>

<script>
import axios from "axios";
export default {
  props: ["index", "product"],
  mounted() {},
  data() {
    return {
      is_searching: false,
      state: this.product["product_id"] ? "old" : "",
      products: [],
    };
  },
  computed: {
    inputName: function () {
      if (this.product.id) {
        return "products." + this.product.id + "";
      }

      return "products.product_" + this.index + "";
    },
  },

  methods: {
    search() {
      const self = this;
      setTimeout(function () {
        self.state = "";
        self.product["product_id"] = null;
        self.is_searching = true;

        if (self.product["name"].length < 2) {
          self.products = [];
          self.is_searching = false;
          return;
        }
        axios
          .get(route("products.search"), {
            params: { query: self.product["name"] },
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
    },

    removeProduct: function () {
      this.$emit("onRemoveProduct", this.product);
    },
  },
};
</script>

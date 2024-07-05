<template>
  <ProductItem
    v-for="(product, index) in products"
    :product="product"
    :key="index"
    :index="index"
    @onRemoveProduct="removeProduct($event)"
  ></ProductItem>
  <BRow class="gy-3">
    <BCol cols="4">
      <BButton type="button" variant="link" @click="addProduct">
        {{ $t("common.add_more") }}
      </BButton>
    </BCol>
  </BRow>
</template>

<script>
import ProductItem from "@/Components/products/productItem.vue";
export default {
  components: {
    ProductItem,
  },
  props: ["data"],
  data: function () {
    return {
      products: this.data ? this.data : [],
    };
  },
  methods: {
    addProduct: function () {
      this.products.push({
        id: null,
        product_id: null,
        name: '',
        quantity: null,
        price: null,
        amount: null,
      });
    },
    removeProduct: function (product) {
      const index = this.products.indexOf(product);
      this.products.splice(index, 1)
      
    },
  },
};
</script>

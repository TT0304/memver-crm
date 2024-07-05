<template>
  <div class="quote-item-list">
    <div class="table">
      <table>
        <thead>
          <tr>
            <th class="name">
              <div class="form-group">
                <label class="required">
                  {{ $t("quotes.name") }}
                </label>
              </div>
            </th>

            <th class="quantity">
              <div class="form-group">
                <label class="required">
                  {{ $t("quotes.quantity") }}
                </label>
              </div>
            </th>

            <th class="price">
              <div class="form-group">
                <label class="required">
                  {{ $t("quotes.price") }}
                  <span class="currency-code">({{ currencyCode }})</span>
                </label>
              </div>
            </th>

            <th class="amount">
              <div class="form-group">
                {{ $t("quotes.amount") }}
                <span class="currency-code">({{ currencyCode }})</span>
              </div>
            </th>

            <th class="discount">
              <div class="form-group">
                <label class="required">
                  {{ $t("quotes.discount") }}
                  <span class="currency-code">({{ currencyCode }})</span>
                </label>
              </div>
            </th>

            <th class="tax">
              <div class="form-group">
                <label class="required">
                  {{ $t("quotes.tax") }}
                  <span class="currency-code">({{ currencyCode }})</span>
                </label>
              </div>
            </th>

            <th class="total">
              <div class="form-group">
                {{ $t("quotes.total") }}
                <span class="currency-code">({{ currencyCode }})</span>
              </div>
            </th>

            <th class="actions"></th>
          </tr>
        </thead>

        <tbody>
          <quote-item
            v-for="(product, index) in products"
            :product="product"
            :errors="errors"
            :key="index"
            :index="index"
            @onRemoveProduct="removeProduct($event)"
          ></quote-item>
        </tbody>
      </table>

      <BRow class="gy-3">
        <BCol cols="4">
          <BButton type="button" variant="link" @click="addProduct">
            + {{ $t("common.add_more") }}
          </BButton>
        </BCol>
      </BRow>
    </div>

    <div class="d-flex justify-content-end quote-total">
      <table style="width: 320px">
        <tr>
          <td>
            {{ $t("quotes.sub-total") }}
            <span class="currency-code">({{ currencyCode }})</span>
          </td>

          <td>-</td>

          <td>
            <div class="form-group">
              <VField name="sub_total">
                <BFormInput
                  name="sub_total"
                  class="form-control"
                  v-model="subTotal"
                  readonly
                >
                </BFormInput>
              </VField>
            </div>
          </td>
        </tr>

        <tr>
          <td>
            {{ $t("quotes.discount") }}
            <span class="currency-code">({{ currencyCode }})</span>
          </td>

          <td>-</td>

          <td>
            <div class="form-group">
              <VField name="discount_amount">
                <BFormInput
                  name="discount_amount"
                  v-model="discountAmount"
                  readonly>
                </BFormInput>
              </VField>
            </div>
          </td>
        </tr>

        <tr>
          <td>
            {{ $t("quotes.tax") }}
            <span class="currency-code">({{ currencyCode }})</span>
          </td>

          <td>-</td>

          <td>
            <div class="form-group">
              <VField name="tax_amount" v-slot="{ field }">
                <BFormInput name="tax_amount" v-model=  "taxAmount" readonly >
                </BFormInput>
              </VField>
            </div>
          </td>
        </tr>

        <tr>
          <td>
            {{ $t("quotes.adjustment") }}
            <span class="currency-code">({{ currencyCode }})</span>
          </td>

          <td>-</td>

          <td>
            <div
              class="form-group"
              :class="errors['adjustment_amount'] ? 'is-invalid' : ''"
            >
              <VField
                type="text"
                name="adjustment_amount"
                class="form-control"
                v-model="adjustmentAmount"
                :class="errors['adjustment_amount'] ? 'is-invalid' : ''"
                :rules="'decimal:4'"
                :label="$t('quotes.adjustment')"
              />
            </div>
          </td>
        </tr>
        <tr v-if="errors['adjustment_amount']">
          <td colspan="3">
            <BFormInvalidFeedback class="d-flex">{{
              errors["adjustment_amount"]
            }}</BFormInvalidFeedback>
          </td>
        </tr>

        <tr>
          <td>
            {{ $t("quotes.grand-total") }}
            <span class="currency-code">({{ currencyCode }})</span>
          </td>

          <td>-</td>

          <td>
            <div class="form-group">
              <VField name="grand_total" 
                  v-model="grandTotal"
                  readonly>
                <BFormInput
                  name="grand_total"
                  v-model="grandTotal"
                  readonly
                >
                </BFormInput>
              </VField>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import QuoteItem from "@/Components/quotes/quote-item.vue";
export default {
  components: {
    QuoteItem,
  },
  props: ["data", "currencyCode", "errors"],

  data: function () {
    return {
      adjustmentAmount: 0,

      products: [
        {
          id: null,
          product_id: null,
          name: "",
          quantity: 0,
          price: 0,
          discount_amount: 0,
          tax_amount: 0,
        },
      ],
    };
  },

  computed: {
    subTotal: function () {
      var total = 0;

      this.products.forEach((product) => {
        total += isNaN(parseFloat(product.price * product.quantity))
          ? 0
          : parseFloat(product.price * product.quantity);
      });

      return total;
    },

    discountAmount: function () {
      var total = 0;

      this.products.forEach((product) => {
        total += isNaN(parseFloat(product.discount_amount))
          ? 0
          : parseFloat(product.discount_amount);
      });

      return total;
    },

    taxAmount: function () {
      var total = 0;

      this.products.forEach((product) => {
        total += isNaN(parseFloat(product.tax_amount))
          ? 0
          : parseFloat(product.tax_amount);
      });

      return total;
    },

    grandTotal: function () {
      var total = 0;

      this.products.forEach((product) => {
        const sTotal =
          parseFloat(product.price * product.quantity) +
          parseFloat(product.tax_amount) -
          parseFloat(product.discount_amount) +
          parseFloat(this.adjustmentAmount);
        total += isNaN(sTotal) ? 0 : sTotal;
      });

      return total;
    },
  },

  methods: {
    addProduct: function () {
      this.products.push({
        id: null,
        product_id: null,
        name: "",
        quantity: null,
        price: null,
        discount_amount: null,
        tax_amount: null,
      });
    },

    removeProduct: function (product) {
      if (this.products.length == 1) {
        this.products = [
          {
            id: null,
            product_id: null,
            name: "",
            quantity: null,
            price: null,
            discount_amount: null,
            tax_amount: null,
          },
        ];
      } else {
        const index = this.products.indexOf(product);

        this.products.splice(index, 1);
      }
    },
  },
};
</script>

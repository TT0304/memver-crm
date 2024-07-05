<script>
export default {
  props: {
    modelValue: {
      type: Object,
      default: () => ({ name: '', price: 0, quantity: 0 }),
    },
  },
  data() {
    return {
    };
  },
  methods: {
    updateField(field, value) {
      // Emit an updated modelValue object with the changed field
      this.$emit('update:modelValue', { ...this.modelValue, [field]: value });
    },
    removeProduct() {
      this.$emit('remove');
    },

  }
};
</script>


<template>
  <BRow class="g-3 border-bottom pb-3 mb-3">
    <BCol lg="12">
      <div class="mb-1">
        <label for="name" class="form-label required">Item</label>
        <BFormInput type="text" class="form-control" id="name" required v-model.trim="modelValue.name" />
        <BFormInvalidFeedback>Please provide a Item.</BFormInvalidFeedback>
      </div>
    </BCol>
    <BCol lg="4">
      <div class="mb-1">
        <label for="price" class="form-label required">Price</label>
        <BFormInput type="number" class="form-control" id="price" required v-model.number="modelValue.price" />
        <BFormInvalidFeedback>Please provide Price.</BFormInvalidFeedback>
      </div>
    </BCol>
    <BCol lg="4">
      <div class="mb-1">
        <label for="quantity" class="form-label required">Quantity</label>
        <BFormInput type="number" class="form-control" id="quantity" required v-model.number="modelValue.quantity" />
        <BFormInvalidFeedback>Please provide Quantity.</BFormInvalidFeedback>
      </div>
    </BCol>
    <BCol lg="4">
      <label for="amount" class="form-label required">Amount</label>
      <div class="mb-1 d-flex align-items-start">
        <div class="flex-grow-1 d-flex align-items-center">
          <BFormInput type="number" class="form-control mx-r2" id="amount" required disabled
            :value="modelValue.price * modelValue.quantity" />
          <BFormInvalidFeedback>Please provide Amount.</BFormInvalidFeedback>
        </div>
        <BButton variant="danger" class="btn-icon waves-effect waves-light ms-2 align-self-center"
          @click="removeProduct()"><i class="ri-delete-bin-5-line"></i></BButton>
      </div>
    </BCol>

  </BRow>
</template>


<style scoped></style>
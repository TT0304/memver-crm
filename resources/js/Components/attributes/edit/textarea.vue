<template>
  <VField
    :name="attribute.code"
    :rules="rules"
    :label="attribute.name"
    v-model="textValue"
    v-slot="{ errors }"
    v-if="!!attribute"
  >
  <BFormTextarea
      v-model="textValue"
      :state="errors[attribute.code]"
      class="form-control"
      :class="{ 'is-invalid': errors.length }"
    ></BFormTextarea>
  </VField>
  <div class="invalid-feedback" v-if="errors&&errors[attribute.code]">{{ errors[attribute.code] }}</div>
</template>

<script>
export default {
  props: ["attribute", "rules", "errors", "data", "cc", "bcc"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  data() {
    return {
      textValue: this.data ?? ''
    }
  },
  computed: {},
};
</script>

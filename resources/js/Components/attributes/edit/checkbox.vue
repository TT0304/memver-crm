<template>
  <VField
    :name="attribute.code"
    type="text"
    :rules="rules"
    class="form-control"
    :class="{ 'is-invalid': errors[attribute.code] }"
  />
  <div class="invalid-feedback">{{ errors[attribute.code] }}</div>
</template>

<script>
import { Field as VField } from "vee-validate";

export default {
  components: {
    VField,
  },
  props: ["attribute", "rules", "errors", "data"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  computed: {
    
  },
};
</script>
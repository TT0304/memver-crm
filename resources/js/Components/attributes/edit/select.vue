<template>
  <VField
    :name="attribute.code"
    :rules="rules"
    :label="attribute.name"
    :value="data ?? null"
    v-if="!!attribute"
  >
    <BFormSelect
      v-model="selectedValue"
      :state="errors&&errors[attribute.code]"
    >
      <!-- Ensure this option's value matches the initial value of selectedValue -->
      <BFormSelectOption :value="null" selected>{{$t('settings.workflows.choose-option')}}</BFormSelectOption>
      <BFormSelectOption
        v-for="(option, index) in options"
        :key="index"
        :value="option.id"
        >{{ option.name }}</BFormSelectOption
      >
    </BFormSelect>
    <BFormInvalidFeedback v-if="errors&&errors[attribute.code]">{{
      errors[attribute.code]
    }}</BFormInvalidFeedback>
  </VField>
</template>

<script>
import { useField } from "vee-validate";
export default {
  props: ["attribute", "rules", "errors", "data", "cc", "bcc"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  setup(props) {
    const { value: selectedValue, validate, errorMessage } = useField(
      props.attribute.code,
      props.rules
    );
    return {
      selectedValue,
      errorMessage,
    };
  },
  data() {
    return {
      options: [],
    };
  },
  mounted() {
    if (this.attribute.lookup_type) {
      const self = this;
      axios
        .get(`${route("settings.attributes.lookup")}/${this.attribute.lookup_type}`, {})
        .then((response) => {
          self.options = response.data;
        })
        .catch((error) => {
          self.options = [];
        });
    } else {
      this.options = this.attribute.options;
    }
  },
  watch: {},
};
</script>

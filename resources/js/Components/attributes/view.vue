<template>
  <div class="attribute-value-row" v-for="(attribute, index) in customAttributes" :key="index">
    <div class="label">
      {{ attribute.name }}
    </div>
    <div class="value">
      <component
        :is="getComponent(attribute.type)"
        :attribute="attribute"
        :value="entity ? entity[attribute.code] : null"
      />
    </div>
  </div>
</template>

<script>
import { defineComponent, reactive, toRefs } from "vue";
import Text from "@/Components/attributes/view/text.vue";
import Checkbox from "@/Components/attributes/view/checkbox.vue";
import Textarea from "@/Components/attributes/view/textarea.vue";
import Textarea_custom from "@/Components/attributes/view/textarea_custom.vue";
import Price from "@/Components/attributes/view/price.vue";
import Select from "@/Components/attributes/view/select.vue";
import Date from "@/Components/attributes/view/date.vue";
import Email_tags from "@/Components/attributes/view/email_tags.vue";
import Subject from "@/Components/attributes/view/subject.vue";
// Import other components as needed

export default defineComponent({
  components: {
    Text,
    Checkbox,
    Textarea,
    Textarea_custom,
    Price,
    Select,
    Date,
    Email_tags,
    Subject
  },
  data() {
    return {
      values: {},
    };
  },
  methods: {
  },
  props: {
    customAttributes: Array,
    currencyCode: String,
    entity: Object,
  },
  setup(props) {
    const getComponent = (type) => {
      switch (type) {
        case "text":
          return "Text";
        case "checkbox":
          return "Checkbox";
        case "textarea":
          return "Textarea";
        case "price":
          return "Price";
        case "select":
          return "Select";
        case "date":
          return "Date";
        case "email_tags":
          return "Email_tags";
        case "textarea_custom":
          return "Textarea_custom";
        case "subejct":
          return "Subject";
        // Add other cases for different types
        default:
          return "div"; // Default or unknown types render nothing
      }
    };
   
    return { getComponent };
  },
});
</script>

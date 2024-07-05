<template>
  <div v-if="customAttributes&&customAttributes.length>0" v-for="(attribute, index) in customAttributes" :key="index" class="mb-3">
    <label
      :for="attribute.code"
      class="form-label"
      :class="{ required: attribute.is_required }"
      v-if="attribute.code != 'cc'&&attribute.code != 'bcc' || (cc&&attribute.code == 'cc' || bcc&&attribute.code == 'bcc')"
    >
      {{ $t("edits." + attribute.code) }}
      <span v-if="attribute.type == 'price'" class="currency-code"
        >({{ currencyCode }})</span
      >
    </label>
      <component
        v-if="!!attribute && (attribute.type == 'subject' || (attribute.code == 'content' && attribute.type == 'textarea_custom'))"
        :is="getComponent(attribute.type)"
        :attribute="attribute"
        :rules="validationRules(attribute)"
        :errors="errors"
        :data="data ? data[attribute.code == 'source' ? 'attachments' : attribute.code] : null"
        :cc="cc"
        :bcc="bcc"
        :placeholdersData="placeholdersData"
        @toggle-cc="toggleCc"
        @toggle-bcc="toggleBcc"
      />
      <component
        v-if="!!attribute && (attribute.type != 'subject' && !(attribute.code == 'content' && attribute.type == 'textarea_custom'))"
        :is="getComponent(attribute.type)"
        :attribute="attribute"
        :rules="validationRules(attribute)"
        :errors="errors"
        :data="data ? data[attribute.code == 'source' ? 'attachments' : attribute.code] : null"
        :cc="cc"
        :bcc="bcc"
        @toggle-cc="toggleCc"
        @toggle-bcc="toggleBcc"
      />
  </div>
</template>

<script>
import { defineComponent, reactive, toRefs, provide } from "vue";
import Text from "@/Components/attributes/edit/text.vue";
import Checkbox from "@/Components/attributes/edit/checkbox.vue";
import Textarea from "@/Components/attributes/edit/textarea.vue";
import Textarea_custom from "@/Components/attributes/edit/textarea_custom.vue";
import Price from "@/Components/attributes/edit/price.vue";
import Select from "@/Components/attributes/edit/select.vue";
import Date from "@/Components/attributes/edit/date.vue";
import Lookup from "@/Components/attributes/edit/lookup.vue";
import Address from "@/Components/attributes/edit/address.vue";
import Email from "@/Components/attributes/edit/email.vue";
import Email_tags from "@/Components/attributes/edit/email_tags.vue";
import Phone from "@/Components/attributes/edit/phone.vue";
import Attachment from "@/Components/attachment/attachment-wrapper.vue";
import Subject from "@/Components/attributes/edit/subject.vue";
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
    Lookup,
    Address,
    Email,
    Email_tags,
    Attachment,
    Phone,
    Subject
  },
  data() {
    return {
      values: {},
      cc: false,
      bcc: false
    };
  },
  methods: {
    handleInput(value, id) {
      this.values[id] = value;
      this.$emit("update:values", this.values);
    },
    toggleCc() {
      this.cc = !this.cc;
    },
    toggleBcc() {
      this.bcc = !this.bcc;
    }
  },
  props: {
    customAttributes: Array,
    currencyCode: String,
    errors: Object,
    data: Object,
    customValidations: Object,
    placeholdersData: Array,
  },
  setup(props) {
    const { customAttributes } = toRefs(props);

    const formValues = reactive({});
    const componentsRefs = reactive({});


    const setAttributeRef = (el, index) => {
      componentsRefs[customAttributes.value[index].code] = el;
    };
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
        case "lookup":
          return "Lookup";
        case "address":
          return "Address";
        case "email":
          return "Email";
        case "email_tags":
          return "Email_tags";
        case "phone":
          return "Phone";
        case "textarea_custom":
          return "Textarea_custom";
        case "attachment":
          return "Attachment";   
        case "subject":
          return "Subject";  
        // Add other cases for different types
        default:
          return "div"; // Default or unknown types render nothing
      }
    };
    const validate = async () => {
      let isValid = true;
      for (const ref in componentsRefs) {
        if (componentsRefs[ref] && componentsRefs[ref].validate) {
          const valid = await componentsRefs[ref].validate();
          isValid = isValid && valid;
        }
      }
      return isValid;
    };
    const validationRules = (attribute) => {
      if (props.customValidations && props.customValidations[attribute.code]) {
        return props.customValidations[attribute.code].join("|");
      }
      let rules = "";
      if (attribute.is_required) {
        rules += "required|";
      }
      if (attribute.rules) {
        // Assuming validation is a string like 'required|email'
        rules += attribute.rules;
      }
      if (attribute.type == "price") rules += "decimal|";
      // Remove trailing '|'
      rules = rules.endsWith("|") ? rules.slice(0, -1) : rules;
      return rules;
    };
    return { formValues, setAttributeRef, getComponent, validationRules, validate };
  },
});
</script>

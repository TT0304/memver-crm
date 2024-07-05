<template>
  <VField
    :name="attribute['code']"
    :label="attribute['name']"
    v-model="textValue"
    :rules="rules"
    v-slot="{ errors }"
  >
    <BInputGroup class="mb-2 d-flex align-items-center">
      <BFormInput
        type="text"
        class="form-control me-2"
        :for="attribute['code']"
        :id="attribute['code']"
        :name="attribute['code']"
        v-model="textValue"
        :class="[errors.length > 0 ? 'is-invalid' : '']"
        @focusout="updateCursorPosition"
      />
        <BFormSelect
          name="subject-placeholders"
          :state="errors[attribute.code]"
          class="me-2 dropdown-menu-end"
          v-model="selectedValue"
          id="subject-placeholders"
          style="min-width: 120px; max-width: 120px"
          @change="insertPlaceholder"
        >
          <BFormSelectOption :value="null" selected>{{$t('settings.email-templates.placeholders')}}</BFormSelectOption>

          <BFormSelectOptionGroup
            v-for="(entity, index) in placeholdersData"
            :key="index"
            :label="entity.text"
          >
            <BFormSelectOption
              v-for="(option, subindex) in entity.menu"
              :key="'sub' + subindex"
              :value="option.value"
              >{{ option.text }}</BFormSelectOption
            >
          </BFormSelectOptionGroup>
        </BFormSelect>
      <BFormInvalidFeedback v-if="errors.length">{{ errors[0] }}</BFormInvalidFeedback>
    </BInputGroup>
  </VField>
</template>

<script>
import { defineRule } from "vee-validate";
export default {
  props: ["rules", "attribute", "data", "cc", "bcc", "errors", "placeholdersData"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners

  data: function () {
    return {
      textValue: this.data || "",
      selectedValue: null
    };
  },
  methods: {
    updateCursorPosition(event) {
      this.cursorPosition = event.target.selectionStart;
    },
    insertPlaceholder(event) {
      const placeholder = event;
      event = '';
      this.selectedValue = null;

      if (this.cursorPosition >= 0) {
        const newContent =
          this.textValue.substring(0, this.cursorPosition) +
          placeholder +
          this.textValue.substring(this.cursorPosition);

        this.textValue = newContent;
        this.cursorPosition += placeholder.length;
      } else if (placeholder) {
        this.textValue += placeholder;
      }
    }
  }
};
</script>

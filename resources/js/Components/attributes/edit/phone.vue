<template>
  <!-- <BInputGroup
    v-for="(email, index) in emails"
    :key="index"
    class="mb-2 d-flex align-items-center"
  > -->
  <div v-for="(contactNumber, index) in contactNumbers" :key="index">
    <VField
      :name="attribute['code'] + '.' + index + '.value'"
      :label="attribute['name']"
      v-model="contactNumber['value']"
      :rules="rules ? rules + '|unique_contact_number' : 'unique_contact_number'"
      v-slot="{ errors }"
    >
      <BInputGroup :key="index" class="mb-2 d-flex align-items-center">
        <BFormInput
          type="text"
          class="form-control me-2"
          :id="attribute['code'] + '.' + index + '.value'"
          :name="attribute['code'] + '.' + index + '.value'"
          v-model="contactNumber['value']"
          :class="[errors.length > 0 ? 'is-invalid' : '']"
        />
        <VField
          :name="attribute['code'] + '.' + index + '.label'"
          :label="attribute['name']"
          v-model="contactNumber['label']"
          :rules="'required'"
        >
          <BFormSelect
            :name="attribute['code'] + '.' + index + '.label'"
            :state="errors[attribute.code]"
            class="me-2 dropdown-menu-end"
            v-model="contactNumber['label']"
            style="min-width: 120px; max-width: 120px"
          >
            <BFormSelectOption value="work">{{ $t("common.work") }}</BFormSelectOption>
            <BFormSelectOption value="home">{{ $t("common.home") }}</BFormSelectOption>
          </BFormSelect>
        </VField>
        <BButton
          variant="danger"
          class="btn-icon waves-effect waves-light"
          v-if="contactNumbers.length > 1"
          style="border-radius: 4px"
          @click="removePhone(contactNumber)"
          ><i class="ri-delete-bin-5-line"></i
        ></BButton>
        <BFormInvalidFeedback v-if="errors.length">{{ errors[0] }}</BFormInvalidFeedback>
      </BInputGroup>
    </VField>
  </div>

  <!-- </BInputGroup> -->
  <BRow class="gy-3">
    <BCol cols="4">
      <BButton type="button" variant="link" @click="addPhone">
        {{ $t("common.add_more") }}
      </BButton>
    </BCol>
  </BRow>
</template>

<script>
import { defineRule } from "vee-validate";
export default {
  props: ["rules", "attribute", "data", "cc", "bcc", "errors"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners

  data: function () {
    return {
      contactNumbers: this.data,
    };
  },

  watch: {
    data: function (newVal, oldVal) {
      if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
        this.contactNumbers = newVal || [{ value: "", label: "work" }];
      }
    },
  },

  created: function () {
    this.extendValidator();

    if (!this.contactNumbers || !this.contactNumbers.length) {
      this.contactNumbers = [
        {
          value: "",
          label: "work",
        },
      ];
    }
  },

  methods: {
    getEmailRule(email) {
      const rulesString = this.rules ? `${this.rules}` : `unique_contact_number`;
      return rulesString;
    },
    addPhone: function () {
      this.contactNumbers.push({
        value: "",
        label: "work",
      });
    },

    removePhone: function (email) {
      const index = this.contactNumbers.indexOf(email);
      this.contactNumbers.splice(index, 1);
    },

    extendValidator: function () {
      defineRule("unique_contact_number", (code) => {
        let filteredEmails = this.contactNumbers.filter((email) => {
          return email.code == code;
        });
        if (filteredEmails.length > 1) {
          return false;
        }
        this.removeUniqueErrors();
        return true;
      });
    },

    isDuplicateExists: function () {
      let emails = this.contactNumbers.map((email) => email.code);

      return emails.some((email, index) => emails.indexOf(email) != index);
    },

    removeUniqueErrors: function () {
      if (!this.isDuplicateExists()) {
        // this.errors.items
        //   .filter((error) => error.rule === "unique")
        //   .map((error) => error.id)
        //   .forEach((id) => {
        //     this.errors.removeById(id);
        //   });
      }
    },
  },
};
</script>

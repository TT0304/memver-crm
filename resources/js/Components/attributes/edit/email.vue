<template>
<!-- <BInputGroup
    v-for="(email, index) in emails"
    :key="index"
    class="mb-2 d-flex align-items-center"
  > -->
    <div v-for="(email, index) in emails" :key="index">
      <VField
        :name="attribute['code'] + '.' + index + '.value'"
        :label="attribute['name']"
        v-model="email['value']"
        :rules="rules ? rules + '|unique_email' : 'unique_email'"
        v-slot="{ errors }"
      >
        <BInputGroup :key="index" class="mb-2 d-flex align-items-center">
          <BFormInput
            type="text"
            class="form-control me-2"
            :id="attribute['code'] + '.' + index + '.value'"
            :name="attribute['code'] + '.' + index + '.value'"
            v-model="email['value']"
          />
<!-- :class="[errors.length > 0 ? 'is-invalid' : '']" -->
          <VField
            :name="attribute['code'] + '.' + index + '.label'"
            :label="attribute['name']"
            v-model="email['label']"
            :rules="'required'"
          >
            <BFormSelect
              :name="attribute['code'] + '.' + index + '.label'"
              :state="errors[attribute.code]"
              class="me-2 dropdown-menu-end"
              v-model="email['label']"
              style="min-width: 120px; max-width: 120px"
            >
              <BFormSelectOption value="work">{{ $t("common.work") }}</BFormSelectOption>
              <BFormSelectOption value="home">{{ $t("common.home") }}</BFormSelectOption>
            </BFormSelect>
          </VField>
          <BButton
            variant="danger"
            class="btn-icon waves-effect waves-light"
            v-if="emails.length > 1"
            style="border-radius: 4px"
            @click="removeEmail(email)"
          ><i class="ri-delete-bin-5-line"></i
        ></BButton>
          <BFormInvalidFeedback v-if="errors.length">{{ errors[0] }}</BFormInvalidFeedback>
        </BInputGroup>
      </VField>
    </div>

<!-- </BInputGroup> -->
    <BRow class="gy-3">
      <BCol cols="4">
        <BButton type="button" variant="link" @click="addEmail">
          {{ $t("common.add_more") }}
        </BButton>
      </BCol>
    </BRow>
    </template>

<script>
import { defineRule } from "vee-validate";
export default {
components: {},
  props: ["rules", "attribute", "data", "errors", "cc", "bcc"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners

  data: function () {
    return {
      emails: this.data,
    };
  },

  watch: {
    data: function (newVal, oldVal) {
      if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
        this.emails = newVal || [{ value: "", label: "work" }];
      }
    },
  },

  created: function () {
    this.extendValidator();

    if (!this.emails || !this.emails.length) {
      this.emails = [
        {
          value: "",
          label: "work",
        },
      ];
    }
  },

  methods: {
    getEmailRule(email) {
      const rulesString = this.rules ? `${this.rules}` : `unique_email`;
              return rulesString;
    },
    addEmail: function () {
      this.emails.push({
        value: "",
        label: "work",
      });
    },

    removeEmail: function (email) {
      const index = this.emails.indexOf(email);
      this.emails.splice(index, 1);
    },

    extendValidator: function () {
      defineRule("unique_email", (value) => {
        let filteredEmails = this.emails.filter((email) => {
          return email.value == value;
        });
        if (filteredEmails.length > 1) {
          return false;
        }
        this.removeUniqueErrors();
        return true;
      });
          },

    isDuplicateExists: function () {
      let emails = this.emails.map((email) => email.value);

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

<template>
  <div class="tags-control form-control" 
    v-if="(cc&&attribute.code == 'cc') || (bcc&&attribute.code == 'bcc') || attribute.code == 'reply_to'" 
    :class="errors[inputEmailName] ? 'is-invalid' : ''"
    >
    <ul class="tags" style="listStyle: none">
      <li class="tag-choice" style="height: fit-content" v-for="(email, index) in emails" :key="index">
        <VField type="hidden" :name="attribute.code + '[' + index + ']'" :value="email" />
        {{ email }}
        <i class="ri-close-line" @click="removeTag(email)"></i>
      </li>

      <li class="tag-input position-relative">
        <!-- <VField
          type="hidden"
          :name="inputEmailName"
          :rules="rules"
          :label="controlLabel"
          v-if="!emails.length && email_term == ''"
        /> -->

        <VField
          :name="inputEmailName"
          :rules="emails.length > 0 ? (email_term.length > 0 && `email`) : (email_term.length > 0 ? `email` : rules)"
          :label="attribute.name"
          v-model="email_term"
          v-slot="{ errors }"
        >
          <BFormInput
            :for="inputEmailName"
            :name="inputEmailName"
            v-model="email_term"
            :placeholder="$t('leads.email-placeholder')"
            :state="errors[inputEmailName]"
            @keydown.enter.prevent="addTag"
            :class="[errors.length > 0 ? 'is-invalid' : '']"
          ></BFormInput>
          
          <BFormInvalidFeedback v-if="errors.length">{{
            errors[0]
          }}</BFormInvalidFeedback>
        </VField>
        <div class="fs-20 position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%)" v-if="attribute.code == 'reply_to'">
          <label @click="toggleCc" class="me-2">
              {{ $t('leads.cc') }}
          </label>

          <label @click="toggleBcc">
              {{ $t('leads.bcc') }}
          </label>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { inject } from 'vue';

export default {
  props: ["attribute", "rules", "data", "errors", "cc", "bcc"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners

  data: function () {
    return {
      emails: this.data ? this.data : [],

      email_term: "",
    };
  },
  computed: {
    inputEmailName() {
      return this.emails.length == 0 ? this.attribute.code + '[0]' : this.attribute.code + '[' + this.emails.length + ']';
    },
  },
  methods: {
    addTag: function () {
      let sanitizedEmail = this.email_term.trim();

      if (this.validateEmail(sanitizedEmail)) {
        this.emails.push(sanitizedEmail);

        this.email_term = "";
      }
    },
    toggleCc() {
      this.$emit('toggle-cc');
    },
    toggleBcc() {
      this.$emit('toggle-bcc');
    },
    removeTag: function (email) {
      const index = this.emails.indexOf(email);

      this.emails.splice(index, 1);
    },

    validateEmail: function (email) {
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      return re.test(String(email).toLowerCase());
    },
  },
};
</script>

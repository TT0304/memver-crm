<template>
  <div class="tags-control form-control" :class="errors[inputEmailName] ? 'is-invalid' : ''">
    <ul class="tags">
      <li class="tag-choice" v-for="(email, index) in emails" :key="index">
        <VField type="hidden" :name="controlName + '[' + index + ']'" :value="email" />
        {{ email }}
        <i class="ri-close-line" @click="removeTag(email)"></i>
      </li>

      <li class="tag-input">
        <!-- <VField
          type="hidden"
          :name="inputEmailName"
          :rules="rules"
          :label="controlLabel"
          v-if="!emails.length && email_term == ''"
        /> -->

        <VField
          type="text"
          :name="inputEmailName"
          :rules="rules ? 'email' : rules + '|email'"
          :label="controlLabel"
          v-model="email_term"
        >
          <BFormInput
            :name="inputEmailName"
            v-model="email_term"
            :placeholder="$t('leads.email-placeholder')"
            :state="errors[inputEmailName]"
            @keydown.enter.prevent="addTag"
          ></BFormInput>
          <BFormInvalidFeedback v-if="errors[inputEmailName]">{{
            errors[inputEmailName]
          }}</BFormInvalidFeedback>
        </VField>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: ["controlName", "controlLabel", "rules", "data", "errors"],

  data: function () {
    return {
      emails: this.data ? this.data : [],

      email_term: "",
    };
  },
  computed: {
    inputEmailName() {
      return this.emails.length == 0 ? this.controlName + '[0]' : this.controlName + '[' + this.emails.length + ']';
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

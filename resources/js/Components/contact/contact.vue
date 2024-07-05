<template>
  <div class="mb-3">
    <label for="person[name]" class="form-label required">{{ $t("leads.name") }}</label>
    <div class="mb-3">
      <VField
        name="person[name]"
        :label="$t('leads.name')"
        rules="required"
        v-model="person.name"
        v-slot="{ errors }"
      >
        <BFormInput
          type="text"
          name="person[name]"
          id="person[name]"
          autocomplete="off"
          v-model="person.name"
          :placeholder="$t('common.start-typing')"
          class="form-control"
          :class="{ 'is-invalid': errors.length }"
          @keyup="search"
        />
        <div class="lookup-results" v-if="state == ''">
          <ul>
            <li
              v-for="(person, index) in persons"
              :key="index"
              @click="addPerson(person)"
            >
              <span>@{{ person.name }}</span>
            </li>

            <li v-if="!persons.length && person['name'].length && !is_searching">
              <span>{{ $t("common.no-result-found") }}</span>
            </li>

            <li
              class="action"
              v-if="person['name'].length && !is_searching"
              @click="addAsNew()"
            >
              <span> + {{ $t("common.add-as") }} </span>
            </li>
          </ul>
        </div>
        <BFormInvalidFeedback v-if="errors.length">{{ errors[0] }}</BFormInvalidFeedback>
      </VField>
    </div>

    <VField
      type="hidden"
      name="person[id]"
      v-model="person.id"
      required
      v-if="person.id"
    />
    <div class="mb-3">
      <label for="contactEmails" class="form-label">{{ $t("leads.email") }}</label>
      <EmailInput
        :attribute="{ code: 'person[emails]', name: 'Email' }"
        :data="person.emails"
        rules="required|email"
      ></EmailInput>
    </div>
    <div class="mb-3">
      <label for="contactEmails" class="form-label">{{ $t("leads.email") }}</label>
      <PhoneInput
        :attribute="{ code: 'person[contact_numbers]', name: 'Contact Numbers' }"
        :data="person.contact_numbers"
      ></PhoneInput>
    </div>
    <div class="mb-3">
      <label for="contactEmails" class="form-label">{{ $t("leads.organization") }}</label>
      <LookupInput
        :attribute="getOrgAttribute()"
        :data="person.organization"
      ></LookupInput>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import EmailInput from "@/Components/attributes/edit/email.vue";
import PhoneInput from "@/Components/attributes/edit/phone.vue";
import LookupInput from "@/Components/attributes/edit/lookup.vue";
export default {
  props: ["attribute", "rules", "data", "organizationAttribute"],
  components: {
    EmailInput,
    PhoneInput,
    LookupInput,
  },
  computed: {},
  mounted() {
  },
  data() {
    return {
      is_searching: false,
      state: this.data ? "old" : "",
      person: this.data
        ? this.data
        : {
            name: "",
          },
      persons: [],
    };
  },
  methods: {
    getOrgAttribute() {
      return {
        ...this.organizationAttribute,
        code: `person['${this.organizationAttribute.code}']`
      }
    },
    search() {
      const context = this;
      setTimeout(function () {
        context.state = "";

        context.person = {
          name: context.person["name"],
        };

        context.is_searching = true;

        if (context.person["name"].length < 2) {
          context.persons = [];

          context.is_searching = false;

          return;
        }

        var self = context;

        axios
          .get(route("contacts.persons.search"), {
            params: { query: context.person["name"] },
          })
          .then(function (response) {
            self.persons = response.data;

            self.is_searching = false;
          })
          .catch(function (error) {
            self.is_searching = false;
          });
      }, 500);
    },

    addPerson: function (result) {
      this.state = "old";

      this.person = result;
    },

    addAsNew: function () {
      this.state = "new";
    },
  },
};
</script>

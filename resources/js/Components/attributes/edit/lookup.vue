<template>
  <div class="search-container" @focusin="handleFocus" @focusout="handleFocusOut">
    <VField
      :name="attribute['code'] + 'name'"
      :label="attribute['name']"
      :rules="rules"
      v-model="search_term"
      v-slot="{ errors }"
    >
      <BFormInput
        type="text"
        :name="attribute['code'] + 'name'"
        :id="attribute['code'] + 'name'"
        :for="attribute['code'] + 'name'"
        autocomplete="off"
        :placeholder="$t('common.start-typing')"
        class="form-control"
        :class="{ 'is-invalid': errors.length }"
        v-model="search_term"
        @keyup="search"
      />
      <div class="lookup-results" v-if="state == '' && search_term">
        <ul>
          <li v-for="(result, index) in results" :key="index" @click="addLookUp(result)">
            <span>@{{ result.name }}</span>
          </li>

          <li
            v-if="
              !results.length &&
              search_term != null &&
              search_term.length &&
              !is_searching
            "
          >
            <span>{{ $t("common.no-result-found") }}</span>
          </li>
        </ul>
      </div>
      <i class="icon loader-active-icon" v-if="is_searching"></i>
      <BFormInvalidFeedback v-if="errors.length">{{ errors[0] }}</BFormInvalidFeedback>
    </VField>
    <VField
      type="hidden"
      :name="attribute['code']"
      v-model="entity_id"
      :rules="rules"
      :label="attribute['name']"
    />
  </div>
</template>

<script>
import axios from "axios";
import eventBus from "@/common/eventBus";

export default {
  props: ["rules", "attribute", "searchRoute", "data"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  data() {
    return {
      search_term: "",
      searchQuery: "",
      suggestions: [],
      showSuggestions: false,
      entity_id: this.data ?? '',
      is_searching: false,
      state: this.data ? "old" : "",
      results: [],
      search_route:
        this.searchRoute ??
        `${route("settings.attributes.lookup")}/${this.attribute.lookup_type}`,
    };
  },
  mounted() {
    console.log(this.data);
  },
  watch: {
    data: function (newVal, oldVal) {
      if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
        this.search_term = newVal ? newVal["name"] : "";
        this.entity_id = newVal ? newVal["id"] : "";
        this.state = newVal ? "old" : "";
      }
    },
  },
  methods: {
    handleFocus() {
      if (this.search_term && this.search_term.length > 0) {
        this.showSuggestions = true; // Show suggestions on focus if there's a query
      }
    },
    handleFocusOut(event) {
      if (!event.relatedTarget || !this.$el.contains(event.relatedTarget)) {
        this.showSuggestions = false; // This hides the suggestions by default
      }
    },
    search() {
      const self = this;
      setTimeout(function () {
        self.state = "";
        self.entity_id = "";
        self.is_searching = true;

        if (self.search_term && self.search_term.length < 2) {
          self.results = [];
          self.is_searching = false;
          return;
        }

        axios
          .get(self.search_route, { params: { query: self.search_term } })
          .then((response) => {
            self.results = response.data;
            self.is_searching = false;
          })
          .catch((error) => {
            self.is_searching = false;
          });
      }, 500);
    },

    addLookUp: function (result) {
      this.state = "old";
      this.entity_id = result["id"];
      this.search_term = result["name"] ?? "";
      eventBus.emit("lookup-added", result);
    },

    createNew: function () {
      this.state = "new";
      this.entity_id = null;
    },
  },
};
</script>

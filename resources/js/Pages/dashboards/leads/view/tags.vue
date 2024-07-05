<template>
  <div class="tags-container">
    <!-- @if (bouncer()->hasPermission('settings.other_settings.tags.create')) -->
    <i class="ri-price-tag-3-line" @click="is_dropdown_open = !is_dropdown_open"></i>
    <!-- @endif -->

    <ul class="tag-list">
      <li
        v-for="(tag, index) in tags"
        :key="index"
        :style="'background-color: ' + (tag.color ? tag.color : '#546E7A')"
      >
        {{ tag.name }}

        <!-- @if (bouncer()->hasPermission('settings.other_settings.tags.delete')) -->
        <i class="ri-close-line text-white" @click="removeTag(tag)"></i>
        <!-- @endif -->
      </li>
    </ul>

    <div class="tag-dropdown" v-if="is_dropdown_open">
      <div class="lookup-results" v-if="!show_form">
        <ul>
          <li class="control-list-item">
            <div class="form-group">
              <input
                type="text"
                class="control"
                v-model="term"
                :placeholder="$t('leads.search_tag')"
                autocomplete="off"
                v-on:keyup="search"
              />

              <i class="icon loader-active-icon" v-if="is_searching"></i>
            </div>
          </li>

          <li v-for="(tag, index) in search_results" @click="addTag(tag)" :key="index">
            <span>{{ tag.name }}</span>
          </li>

          <li v-if="!search_results.length && term.length && !is_searching">
            <span>{{ $t("common.no-result-found") }}</span>
          </li>

          <li class="action" @click="show_form = true">
            <span> + {{ $t("leads.add-tag") }} </span>
          </li>
        </ul>
      </div>

      <div class="form-container" v-else>
        <VForm label="tag-form" v-slot="{ errors }" @submit="createTag">
          <div class="form-group">
            <label class="required">{{ $t("leads.name") }}</label>

            <VField
              type="text"
              name="name"
              rules="required"
              v-model="tag.name"
              :label="$t('leads.name')"
            >
              <BFormInput
                type="text"
                name="name"
                class="form-control"
                v-model="tag.name"
                :state="errors['name']"
                :class="errors['name'] ? 'is-invalid' : ''"
              >
              </BFormInput>
              <BFormInvalidFeedback v-if="errors['name']">{{
                errors["name"]
              }}</BFormInvalidFeedback>
            </VField>
          </div>

          <div class="form-group">
            <label>{{ $t("leads.color") }}</label>

            <div class="color-list">
              <span
                v-for="(color, index) in colors"
                :key="index"
                :style="'background:' + color"
                :class="{ active: tag.color == color }"
                @click="tag.color = color"
              >
              </span>
            </div>
          </div>

          <div class="form-group button-group">
            <button
              type="button"
              class="btn btn-sm btn-secondary-outline"
              @click="show_form = false"
            >
              {{ $t("leads.cancel") }}
            </button>

            <button type="submit" class="btn btn-sm btn-primary">
              {{ $t("leads.save") }}
            </button>
          </div>
        </VForm>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { successNotify, errorNotify } from "@/common/toast";

export default {
  props: ["lead"],
  data() {
    return {
      is_dropdown_open: false,
      term: "",
      is_searching: false,
      tags: this.lead.tags ?? [],
      search_results: [],
      tag: {
        name: "",
        color: "",
        lead_id: this.lead.id,
      },
      colors: ["#337CFF", "#FEBF00", "#E5549F", "#27B6BB", "#FB8A3F", "#43AF52"],
      show_form: false,
    };
  },
  setup() {},
  watch: {},
  methods: {
    search() {
      setTimeout(function () {
        this.is_searching = true;
        if (this.term.length < 2) {
          this.search_results = [];
          this.is_searching = false;
          return;
        }

        var self = this;
        axios
          .get(route("settings.tags.search"), {
            params: { query: this.term },
          })
          .then(function (response) {
            self.tags.forEach(function (addedTag) {
              response.data.forEach(function (tag, index) {
                if (tag.id == addedTag.id) {
                  response.data.splice(index, 1);
                }
              });
            });

            self.search_results = response.data;

            self.is_searching = false;
          })
          .catch(function (error) {
            self.is_searching = false;
          });
      }, 500);
    },
    createTag: function () {
      var self = this;

      axios
        .post(`${route("settings.tags.store")}`, self.tag)
        .then((response) => {
          self.addTag(response.data.tag);
        })
        .catch((error) => {
          errorNotify(error.response.data.errors["name"][0]);
        });
    },

    addTag: function (tag) {
      var self = this;

      axios
        .post(route("leads.tags.store", this.lead.id), tag)
        .then((response) => {
          self.is_dropdown_open = self.show_form = false;
          self.search_results = [];
          self.term = "";
          self.tags.push(tag);
          successNotify([response.data.message]);
        })
        .catch((error) => {
          errorNotify([error.message ?? error.response.data.message]);
        });
    },

    removeTag: function (tag) {
      var self = this;

      axios
        .delete(route("leads.tags.delete", { lead_id: this.lead.id, tag_id: tag.id }))
        .then(function (response) {
          const index = self.tags.indexOf(tag);

          self.tags.splice(index, 1);
          successNotify([response.data.message]);
        })
        .catch(function (error) {
          successNotify([error.response.data.message]);
        });
    },
  },
  components: {},
};
</script>

<style lang="scss"></style>

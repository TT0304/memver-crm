<script>
export default {
  props: {
    arrays: {
      type: Array,
      default: () => [],
    },
    placeholderText: {
      type: String,
      default: "",
    },
    noSuggestionMsg: {
      type: String,
      default: "",
    },
    modelValue: {
      type: Number,
      default: () => (null),
    },
  },
  data() {
    return {
      searchQuery: '',
      suggestions: [],
      showSuggestions: false, // New property to control the visibility of the suggestion list
    };
  },
  methods: {
    fetchSuggestions() {
      this.showSuggestions = true; // Show suggestions when fetching
      if (!this.arrays) return;
      this.suggestions = this.arrays.filter(item =>
        item.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    selectSuggestion(item) {
      this.searchQuery = item;
      this.suggestions = [];
      this.showSuggestions = false; // Hide suggestions after selection
      this.$emit('update:modelValue', item.id);
    },
    handleFocus() {
      if (this.searchQuery.length > 0) {
        this.showSuggestions = true; // Show suggestions on focus if there's a query
      }
    },
    handleFocusOut(event) {
      // Implement logic to determine if you should keep the suggestions visible
      // For example, checking if the relatedTarget is not one of the suggestions
      if (!event.relatedTarget || !this.$el.contains(event.relatedTarget)) {
        this.showSuggestions = false; // This hides the suggestions by default
      }
    }
  }
};
</script>


<template>
  <div class="search-container" @focusin="handleFocus" @focusout="handleFocusOut">
    <input
      type="text"
      v-model="searchQuery"
      class="form-control"
      @input="fetchSuggestions"
      :placeholder="placeholderText"
    />
    <ul v-if="showSuggestions" class="suggestions-list">
      <template v-if="suggestions.length">
        <li v-for="(item, index) in suggestions" :key="index" @click="selectSuggestion(item)">
          {{ item.name }}
        </li>
      </template>
      <li v-else class="not-selectable">{{ noSuggestionMsg }}</li>
    </ul>
  </div>
</template>


<style scoped>
.suggestions-list {
  list-style: none;
  margin: 0;
  padding: 0;
  border: 1px solid #ccc;
  border-top: none;
}

.suggestions-list li {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
}

.suggestions-list li:hover {
  background-color: #f0f0f0;
}
</style>
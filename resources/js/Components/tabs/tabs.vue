<template>
  
  <div>
    <div class="tabs">
      <ul>
        <li
          :key="index"
          v-for="(tab, index) in tabs"
          :class="{ active: tab.isActive, 'has-error': tab.hasError }"
          class="horizontal-li"
          @click="selectTab(tab)"
        >
          <a>{{ tab.name }}</a>
        </li>
      </ul>
    </div>

    <div class="tabs-content" v-if="!hideTabsContent">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import eventBus from "@/common/eventBus";
export default {
  props: ["tabsCollection", "eventKey", "eventData", "eventValueKey"],

  data: function () {
    return {
      tabs: [],

      hideTabsContent: false,
    };
  },

  mounted: function () {
    if (this.$slots.length > 0) {
      this.tabs = this.$slots;
    } else {
      this.hideTabsContent = true;

      this.tabs = this.tabsCollection;
    }

    this.selectDefaultTab();
  },

  watch: {
    tabsCollection: function (newValue, oldValue) {
      this.tabs = newValue;
    },
  },

  methods: {
    selectDefaultTab: function () {
      var hasActiveTab = false;

      this.tabs.forEach((tab) => {
        if (tab.isActive) {
          hasActiveTab = true;
        }
      });

      if (!hasActiveTab) {
        this.tabs[0].isActive = true;
      }
    },

    selectTab: function (selectedTab) {
      this.tabs.forEach((tab) => {
        tab.isActive = tab.name == selectedTab.name;
      });

      if (!this.eventKey) {
        return;
      }

      var eventData = this.eventData ? this.eventData : {};

      eventData[this.eventValueKey] = selectedTab.key;

      eventBus.emit(this.eventKey, eventData);
    },
  },
};
</script>
<style scoped>
ul {
  padding-left: 0px;
}
</style>
<template>
  <div
    :class="`pagination ${tabView ? 'tab-view' : 'full-view'}`"
    v-if="records.data.length > 0"
  >
    <template v-if="tabView">
      <a class="page-item previous disabled d-flex align-items-center" v-if="!records.prev_page_url">
        <i class="ri-arrow-left-s-fill"></i>
      </a>

      <a
        v-else
        id="previous"
        class="page-item previous d-flex align-items-center"
        @click="
          changePage({
            url: records.prev_page_url,
            page_number: records.current_page - 1,
          })
        "
      >
        <i class="ri-arrow-left-s-fill"></i>
      </a>

      <a
        id="next"
        class="page-item next d-flex align-items-center"
        v-if="records.next_page_url"
        @click="
          changePage({
            url: records.next_page_url,
            page_number: records.current_page + 1,
          })
        "
      >
        <i class="ri-arrow-right-s-fill"></i>
      </a>

      <a class="page-item next disabled d-flex align-items-center" v-else>
        <i class="ri-arrow-right-s-fill"></i>
      </a>
    </template>

    <template v-else>
      <a
        v-for="(link, index) in records.links"
        :key="index"
        href="javascript:void(0);"
        :data-page="link.url"
        :class="`page-item ${index == 0 ? 'previous' : ''} ${
          link.active ? 'active' : ''
        } ${index == records.links.length - 1 ? 'next' : ''}`"
        @click="
          changePage({
            url: link.url,
            page_number: records.current_page,
          })
        "
        class=" d-flex align-items-center"
      >
        <i class="ri-arrow-left-s-fill" v-if="index == 0"></i>
        <i
          class="ri-arrow-right-s-fill"
          v-else-if="index == records.links.length - 1"
        ></i>
        <span v-text="link.label" v-else></span>
      </a>
    </template>
  </div>
</template>

<script>
import { mapState } from "vuex";
import eventBus from "@/common/eventBus";
export default {
  props: ["tabView", "perPage"],

  data: function () {
    return {};
  },

  computed: {
    ...mapState('data', {
      tableData: (state) => state.tableData,
    }),

    records: function () {
      return this.tableData.records;
    },
  },

  methods: {
    changePage: function ({ url, page_number }) {
      eventBus.emit("updateFilter", {
        key: "page",
        value: page_number,
      });
    },
  },
};
</script>

<template>
  <BCard no-body>
    <BCardBody>
      <BRow class="g-2">
        <BCol class="auto-col">
          <div class="table-data" style="min-height: 350px">
            <div class="table-header">
              <slot name="table-header"></slot>

              <div class="table-action">
                <span
                  class="export-import"
                  @click="isOpen = true"
                  v-if="tableData.export"
                >
                  <i class="icon ri-file-transfer-line me-1"></i>

                  <span>{{ $t("ui.datagrid.export") }}</span>
                </span>

                <slot name="table-action"></slot>
              </div>
            </div>

            <sidebar-filter></sidebar-filter>

            <div class="table-body" v-if="Object.keys(tableData).length > 0">

              <TableFilterComponent>
                <template v-slot:extra-filters>
                  <slot name="extra-filters"></slot>
                </template>
              </TableFilterComponent>

              <table v-if="tableData.records.total">
                <thead-component></thead-component>

                <tbody-component :result-loaded="resultLoaded"></tbody-component>
              </table>

              <div class="empty-table" v-else>
                <div>
                  <img src="@assets/images/empty-table-icon.svg" />

                  <span>{{ $t("ui.datagrid.no-records") }}</span>
                </div>
              </div>
            </div>

            <!-- <form
        method="POST"
        :action="`${baseURL}/export`"
        ref="exportForm"
        @submit="exportData"
      >
        <modal id="export" :is-open="isOpen">
          <h3 slot="header-title">{{ $t("ui.datagrid.download") }}</h3>
  
          <div slot="header-actions">
            <button class="btn btn-sm btn-secondary-outline" @click="isOpen = false">
              {{ $t("ui.datagrid.cancel") }}
            </button>
  
            <button type="submit" class="btn btn-sm btn-primary">
              {{ $t("ui.datagrid.export") }}
            </button>
          </div>
  
          <div slot="body">
            <div class="form-group">
              <label for="format" class="required">
                {{ $t("ui.datagrid.select-format") }}
              </label>
  
              <input type="hidden" name="gridClassName" :value="tableData.className" />
  
              <select name="format" class="control" v-validate="'required'">
                <option value="xls">XLS</option>
                <option value="csv">CSV</option>
              </select>
            </div>
          </div>
        </modal>
      </form> -->
          </div>
        </BCol>
      </BRow>
    </BCardBody>
  </BCard>
</template>

<script>
import { mapState, mapActions } from "vuex";
import TableFilterComponent from "@/Components/datagrid/filters.vue";
import TbodyComponent from "@/Components/datagrid/table-body.vue";
import TheadComponent from "@/Components/datagrid/table-head.vue";
import SidebarFilter from "@/Components/datagrid/side-filter.vue";
import axios from "axios";
import eventBus from "@/common/eventBus";
import { successNotify, errorNotify } from "@/common/toast";
export default {
  components: {
    TableFilterComponent,
    TbodyComponent,
    TheadComponent,
    SidebarFilter,
  },
  props: ["dataSrc"],
  data: function () {
    return {
      // csrfToken: $('meta[name="csrf-token"]').attr("content"),
      isOpen: false,
      pageLoaded: false,
      previousURL: null,
      resultLoaded: true,
      baseURL: window.baseURL,
    };
  },

  computed: {
    ...mapState("data", {
      tableData: (state) => state.tableData,
      filters: (state) => state.filters,
    }),
  },

  mounted: function () {
    let search = window.location.search;
    const initialIndex = search.indexOf("?");

    if (initialIndex > -1) {
      search = search.replace("?", "");
    }

    this.getData({ self: this, newParams: search });

    eventBus.on("refresh_table_data", (data) => {
      data["self"] = this;
      this.getData(data);
    });
  },

  methods: {
    ...mapActions("data", ["updateTableData", "toggleSidebarFilter", "updateFilters"]),

    getData: ({ newParams, self, url, usePrevious }) => {
      if (self.resultLoaded) {
        self.resultLoaded = false;

        const urlParams = new URLSearchParams(window.location.search);
        if (usePrevious) {
          url = self.previousURL;
        } else {
          url = url ? url : `${self.dataSrc}?${newParams}`;
          self.previousURL = url;
        }

        // if (urlParams.size > 0) {
        //   urlParams.keys().forEach((paramKey) => {
        //     url += `&${paramKey}=${urlParams.get(paramKey)}`;
        //   });
        // }

        self.$http
          .get(url)
          .then((response) => {
            self.pageLoaded = self.resultLoaded = true;
            self.updateTableData(response.data);

            if (newParams || newParams == "") {
              self.updatedURI(newParams);
            }
          })
          .catch((error) => {
            const actualFilters = self.filters.filter(
              (filter) => !(filter.column == "view_type" && filter.val == "table")
            );

            if (error.response.status == 500 && actualFilters.length > 0) {
              self.updateFilters(
                self.filters.filter(
                  (filter) => filter.column == "view_type" && filter.val == "table"
                )
              );

              self.toggleSidebarFilter();
              errorNotify(error?.response?.data?.message);    
            }

            self.pageLoaded = self.resultLoaded = true;
          });
      }
    },

    updatedURI: function (params) {
      let newURL = window.location.pathname + `${params != "" ? "?" + params : ""}`;
      window.history.pushState({ path: newURL }, "", newURL);
    },

    exportData: function () {
      this.$refs.exportForm.submit();

      var self = this;

      setTimeout(() => {
        self.isOpen = false;
      }, 0);
    },
  },
};
</script>

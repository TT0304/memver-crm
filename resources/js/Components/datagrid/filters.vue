<template>
  <div class="grid-container">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <div class="d-flex align-items-center justify-content-between gap-2">
          <!-- Search Box -->
          <div lg="3" class="" v-if="tableData.enableSearch">
            <div class="search-box">
              <input
                type="text"
                class="form-control search"
                :placeholder="$t('ui.datagrid.search')"
                v-model="searchValue"
                @keyup="searchCollection(searchValue)"
              />
              <i class="ri-search-line search-icon"></i>
              <i
                v-if="searchValue"
                class="ri ri-close-line fs-24 search-close-icon"
                @click="searchValue = ''"
              ></i>
            </div>
          </div>
          <!-- Table action for selected rows -->
          <div v-if="selectedTableRows.length > 0" class="d-flex gap-2">
            <VField name="mass_action">
              <BFormSelect
                name="mass_action"
                class="form-control"
                v-model="massActionValue"
                :rules="'required'"
              >
                <BFormSelectOption value="NA" disbaled="disbaled">
                  {{ $t("ui.datagrid.massaction.select_action") }}
                </BFormSelectOption>

                <BFormSelectOption
                  :value="massAction"
                  :key="index"
                  v-for="(massAction, index) in tableData.massActions"
                >
                  {{ $t(massAction.label) }}
                </BFormSelectOption>
              </BFormSelect>
              <BFormSelect
                class="control"
                v-model="massActionOptionValue"
                name="update-options"
                :rules="'required'"
                v-if="massActionValue.type == 'update'"
              >
                <BFormSelectOption value="NA" disbaled="disbaled">
                  {{ $t("ui.datagrid.massaction.select_action") }}
                </BFormSelectOption>

                <BFormSelectOption
                  :key="key"
                  v-for="(option, key) in massActionValue.options"
                  :value="option"
                >
                  {{ key }}
                </BFormSelectOption>
              </BFormSelect>
              <BButton type="submit" class="btn btn-sm btn-primary" @click="onSubmit">
                {{ $t("ui.datagrid.submit") }}
              </BButton>
            </VField>
          </div>
        </div>
      </div>

      <div class="col-auto col-ms-auto">
        <div
          class="filter-right d-flex align-items-center justify-content-end gap-2"
          v-if="tableData.enablePerPage"
        >
          <label for="perPage">
            {{ $t("ui.datagrid.items-per-page") }}
          </label>
          <VField id="perPage" name="perPage" class="form-control" v-model="perPage">
            <BFormSelect name="perPage" v-model="perPage" @change="paginate">
              <BFormSelectOption value="10">10</BFormSelectOption>
              <BFormSelectOption value="20">20</BFormSelectOption>
              <BFormSelectOption value="30">30</BFormSelectOption>
              <BFormSelectOption value="40">40</BFormSelectOption>
              <BFormSelectOption value="50">50</BFormSelectOption>
            </BFormSelect>
          </VField>

          <pagination-component
            tab-view="true"
            :per-page="perPage"
            v-if="!tableData.tabFilters.length > 0"
          ></pagination-component>

          <slot name="extra-filters"></slot>
          <BButton
            variant="primary"
            class="btn-suffix-label"
            v-if="tableData.enableFilters"
            @click="toggleSidebarFilter"
          >
            <div
              class=""
              data-bs-toggle="offcanvas"
              data-bs-target="#theme-settings-offcanvas"
              aria-controls="theme-settings-offcanvas"
              id="mdi-cog1"
            ></div>
            {{ $t("ui.datagrid.filter.title") }}
            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
          </BButton>
        </div>
      </div>
    </div>
    <div v-if="tableData.tabFilters.length > 0" class="mt-2 tabs-container">
      <tabs-component
        event-value-key="value"
        event-key="updateFilter"
        :tabs-collection="tableData.tabFilters[0].values"
        :class="`${tableData.tabFilters[0].type} tabs-left-container`"
        :event-data="{
          key: tableData.tabFilters[0].key,
          cond: tableData.tabFilters[0].condition,
        }"
        v-if="tableData.tabFilters && tableData.tabFilters[0]"
      >
      </tabs-component>
      <div v-else></div>

      <div class="tabs-right-container">
        <section>
          <pagination-component
            tab-view="true"
            :per-page="perPage"
          ></pagination-component>
        </section>

        <tabs-component
          event-value-key="value"
          event-key="updateFilter"
          :class="`${tableData.tabFilters[1].type}`"
          :tabs-collection="tableData.tabFilters[1].values"
          v-if="tableData.tabFilters && tableData.tabFilters[1]"
          :event-data="{
            key: tableData.tabFilters[1].key,
            cond: tableData.tabFilters[1].condition,
          }"
        ></tabs-component>

        <!-- <div class="custom-design-container dropdown-list">
            <label>
                {{ $t("ui.datagrid.filter.date_range") }}
            </label>

            <i
                class="icon close-icon"
                data-close-container="true"
                @click="toggleCustomTabFilter(false)"
            ></i>

            <date-range-basic
                date-range-key="duration"
                :start-date="custom_filter[0]"
                :end-date="custom_filter[1]"
                @onChange="changeDateRange($event)"
            ></date-range-basic>

            <button
                type="button"
                data-close-container="true"
                class="btn btn-sm btn-primary"
                @click="applyCustomFilter"
            >
                {{ $t("ui.datagrid.filter.done") }}
            </button>
        </div> -->
      </div>
    </div>
    <div class="filtered-tags" v-if="myFilters.length > 0">
      <template v-for="(filter, index) in myFilters">
        <div
          :key="index"
          class="filter-tag"
          v-if="ignoreDisplayFilter.indexOf(filter.column) == -1"
        >
          <span>{{ $t("ui.datagrid.filter." + filter.column) }}</span>
          <span class="wrapper">
            {{ filter.prettyValue || decodeURIComponent(filter.val) }}
            <i class="ri-close-circle-line fs-18" @click="removeFilter(filter)"></i>
          </span>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import eventBus from "@/common/eventBus";
import PaginationComponent from "@/Components/datagrid/pagination.vue";
import TabsComponent from "@/Components/tabs/tabs.vue";
import { successNotify, errorNotify } from "@/common/toast";
export default {
  props: ["tabs"],
  components: {
    PaginationComponent,
    TabsComponent,
  },

  data: function () {
    return {
      type: null,
      myFilters: [],
      perPage: 10,
      debounce: {},
      sortAsc: "asc",
      searchValue: "",
      sortDesc: "desc",
      stringValue: null,
      booleanValue: null,
      massActionValue: "NA",
      sidebarFilter: false,
      stringCondition: null,
      numberCondition: null,
      booleanCondition: null,
      datetimeCondition: null,
      massActionOptionValue: "NA",
      custom_filter: [null, null],
      url: new URL(window.location.href),
      ignoreDisplayFilter: ["view_type"],
    };
  },

  computed: {
    ...mapState("data", {
      tableData: (state) => state.tableData,
      customTabFilter: (state) => state.customTabFilter,
      selectedTableRows: (state) => state.selectedTableRows,
      filters: (state) => state.filters,

    }),

    columns: function () {
      return this.tableData.columns;
    },

    customTabFilters: function () {
      return this.tableData.customTabFilters;
    },
  },

  watch: {
    myFilters: function (newValue, oldValue) {
      this.updateFilters(newValue);
      let duration = newValue.find((filter) => filter.column == "duration");
      if (duration) {
        duration = duration.val.split(",");
        let timestamp = Date.parse(duration[0]);
        if (isNaN(timestamp) == false) {
          this.custom_filter = duration;
        }
      }
    },

    filters: function (newValue, oldValue) {
      this.myFilters = newValue;

      if (this.myFilters.length == 0) {
        this.custom_filter = [null, null];
      }

      this.makeURL();
    },

    columns: function (newValue, oldValue) {
      if (newValue.length != oldValue.length) {
        this.updateFilterValue();
      }
    },
  },

  mounted: function () {
    this.setParamsAndUrl();

    if (this.myFilters.length) {
      for (let i = 0; i < this.myFilters.length; i++) {
        if (this.myFilters[i].column === "perPage") {
          this.perPage = this.myFilters[i].val;
        }
      }
    }

    // $("body").click((event) => {
    //   if (
    //     ((typeof event.target.className == "string" &&
    //       event.target.className?.includes("custom-design-container")) ||
    //       $(event.target).parents(".flatpickr-calendar").length ||
    //       $(event.target).parents(".custom-design-container").length) &&
    //     !$(event.target).attr("data-close-container")
    //   ) {
    //     event.stopPropagation();
    //   }
    // });

    eventBus.on("updateFilter", (data) => {
      if (this.customTabFilters.includes(data.key) && data.value == "custom") {
        setTimeout(() => {
          document.querySelector(".custom-design-container").toggle();
        });
      } else {
        this.updateFilter(data);
        
        this.toggleCustomTabFilter(false);
      }
    });
  },

  methods: {
    ...mapActions("data", ["toggleSidebarFilter", "selectAllRows", "updateFilters", "toggleCustomTabFilter"]),

    nullify: function () {
      this.stringCondition = null;
      this.datetimeCondition = null;
      this.booleanCondition = null;
      this.numberCondition = null;
    },

    searchCollection: function (searchValue) {
      clearTimeout(this.debounce["search"]);

      let sanitizedSearchValue = searchValue.trim();

      this.debounce["search"] = setTimeout(() => {
        this.formURL("search", "all", sanitizedSearchValue, "Search");
      }, 1000);
    },

    setParamsAndUrl: function () {
      let params = new URL(window.location.href).search;

      if (params.slice(1, params.length).length > 0) {
        this.arrayFromUrl();
      }

      this.setActiveTabs();
    },

    formURL: function (column, condition, response, label) {
      let obj = {};

      if (
        column === "" ||
        condition === "" ||
        response === "" ||
        column === null ||
        condition === null ||
        response === null
      ) {
        this.updateFilter({
          key: column,
          value: "",
        });

        return false;
      } else {
        if (this.myFilters.length > 0) {
          if (column !== "sort" && column !== "search") {
            let filterRepeated = false;

            for (let j = 0; j < this.myFilters.length; j++) {
              if (this.myFilters[j].column === column) {
                if (
                  this.myFilters[j].cond === condition &&
                  this.myFilters[j].val === response
                ) {
                  filterRepeated = true;

                  return false;
                } else if (
                  this.myFilters[j].cond === condition &&
                  this.myFilters[j].val !== response
                ) {
                  filterRepeated = true;

                  this.myFilters[j].val = response;

                  this.makeURL();
                }
              }
            }

            if (filterRepeated === false) {
              obj.column = column;
              obj.cond = condition;
              obj.val = response;
              obj.label = label;

              this.myFilters.push(obj);
              obj = {};

              this.makeURL();
            }
          }

          if (column === "search") {
            let search_found = false;

            for (let j = 0; j < this.myFilters.length; j++) {
              if (this.myFilters[j].column === "search") {
                this.myFilters[j].column = column;
                this.myFilters[j].cond = condition;
                this.myFilters[j].val = encodeURIComponent(response);
                this.myFilters[j].label = label;

                this.makeURL();
              }
            }

            for (let j = 0; j < this.myFilters.length; j++) {
              if (this.myFilters[j].column === "search") {
                search_found = true;
              }
            }

            if (search_found === false) {
              obj.column = column;
              obj.cond = condition;
              obj.val = encodeURIComponent(response);
              obj.label = label;

              this.myFilters.push(obj);

              obj = {};

              this.makeURL();
            }
          }
        } else {
          obj.column = column;
          obj.cond = condition;
          obj.val = encodeURIComponent(response);
          obj.label = label;

          this.myFilters.push(obj);

          obj = {};

          this.makeURL();
        }
      }
    },

    makeURL: function () {
      let newParams = "";

      for (let i = 0; i < this.myFilters.length; i++) {
        if (
          this.myFilters[i].column == "status" ||
          this.myFilters[i].column == "value_per_locale" ||
          this.myFilters[i].column == "value_per_channel" ||
          this.myFilters[i].column == "is_unique"
        ) {
          if (this.myFilters[i].val.includes("True")) {
            this.myFilters[i].val = 1;
          } else if (this.myFilters[i].val.includes("False")) {
            this.myFilters[i].val = 0;
          }
        }

        let condition = "";
        if (this.myFilters[i].cond !== undefined) {
          condition = "[" + this.myFilters[i].cond + "]";
        }

        if (i == 0) {
          newParams = this.myFilters[i].column + condition + "=" + this.myFilters[i].val;
        } else {
          newParams =
            newParams +
            "&" +
            this.myFilters[i].column +
            condition +
            "=" +
            this.myFilters[i].val;
        }
      }

      eventBus.emit("refresh_table_data", {
        newParams,
      });
    },

    arrayFromUrl: function () {
      let obj = {};
      const processedUrl = this.url.search.slice(1, this.url.length);
      let splitted = [];
      let moreSplitted = [];

      splitted = processedUrl.split("&");

      for (let i = 0; i < splitted.length; i++) {
        moreSplitted.push(splitted[i].split("="));
      }

      for (let i = 0; i < moreSplitted.length; i++) {
        const key = decodeURI(moreSplitted[i][0]);
        let value = decodeURI(moreSplitted[i][1]);

        if (value.includes("+")) {
          value = value.replace("+", " ");
        }

        obj.column = key.replace("]", "").split("[")[0];
        obj.cond = key.replace("]", "").split("[")[1];

        obj.val = value;

        if (obj?.column?.replaceAll) {
          obj.prettyColumn = `${obj.column.replaceAll("_", " ")}`;
        }

        switch (obj.column) {
          case "search":
            obj.label = "Search";
            break;

          case "sort":
            obj.prettyValue = `${obj.cond.replaceAll("_", " ")} - ${obj.val}`;
            break;

          default:
            break;
        }

        if (obj.cond == "bw") {
          let timestamp = Date.parse(obj.val.split(",")[0]);

          if (isNaN(timestamp) == false) {
            obj.prettyValue = `${obj.val.replaceAll(",", " - ")}`;
          }
        }

        if (obj.column !== undefined && obj.val !== undefined) {
          this.myFilters.push(obj);
        }

        obj = {};
      }
    },

    paginate: function (e) {
      for (let i = 0; i < this.myFilters.length; i++) {
        if (this.myFilters[i].column == "perPage") {
          this.myFilters.splice(i, 1);
          break;
        }
      }

      this.myFilters.push({
        column: "perPage",
        cond: "eq",
        val: parseInt(e),
      });

      this.makeURL();
    },

    updateFilter: function ({ key, value, cond }) {
      this.myFilters = this.myFilters.filter((filter) => filter.column != key);

      if (value && value != "" && value != ",") {
        let data = {
          column: key,
          val: value,
        };

        if (data?.column?.replaceAll) {
          data.prettyColumn = `${data.column.replaceAll("_", " ")}`;
        }

        if (cond) {
          data["cond"] = cond;
        }

        if (key == "sort") {
          data.prettyValue = `${data.cond.replaceAll("_", " ")} - ${data.val}`;
        } else if (data.cond == "bw") {
          let timestamp = Date.parse(data.val.split(",")[0]);

          if (isNaN(timestamp) == false) {
            data.prettyValue = `${data.val.replaceAll(",", " - ")}`;
          }
        }

        this.myFilters.push(data);

        this.updateFilterValue();
      }

      this.makeURL();
    },

    setActiveTabs: function () {
      let defaultSelectedIndex = [];

      for (const index in this.tableData.tabFilters) {
        for (const tabValueIndex in this.tableData.tabFilters[index].values) {
          if (this.tableData.tabFilters[index].values[tabValueIndex].isActive) {
            defaultSelectedIndex[index] = tabValueIndex;
          }

          this.tableData.tabFilters[index].values[tabValueIndex].isActive = false;
        }
      }

      for (const index in this.tableData.tabFilters) {
        let applied = false;

        this.myFilters.forEach((filter) => {
          if (filter.column == this.tableData.tabFilters[index].key) {
            for (const tabValueIndex in this.tableData.tabFilters[index].values) {
              if (
                this.tableData.tabFilters[index].values[tabValueIndex].key ==
                  filter.val ||
                (filter.cond == "bw" &&
                  this.tableData.tabFilters[index].values[tabValueIndex].key == "custom")
              ) {
                applied = true;
                this.tableData.tabFilters[index].values[tabValueIndex].isActive = true;
              }
            }
          }
        });

        if (!applied) {
          this.tableData.tabFilters[index].values[
            defaultSelectedIndex[index]
          ].isActive = true;
        }
      }
    },

    onSubmit: function (event) {
      this.$root.pageLoaded = false;

      this.$root.toggleButtonDisable(true);

      if (!this.massActionValue.action) {
        this.$root.pageLoaded = true;

        this.$root.toggleButtonDisable(false);
        // this.$bvToast.toast('Toast body content', {
        //   title: `Variant ${variant || 'default'}`,
        //   variant: variant,
        //   solid: true
        // })
        errorNotify(this.$t("ui.datagrid.mandatory_mass_action"));    
        return;
      }

      if (this.massActionValue.type !== "delete" && this.massActionOptionValue === "NA") {
        this.$root.pageLoaded = true;

        this.$root.toggleButtonDisable(false);
        errorNotify(this.$t("ui.datagrid.mandatory_mass_action"));    
        return;
      }

      if (!confirm(this.$t("ui.datagrid.massaction.delete"))) {
        this.$root.pageLoaded = true;

        this.$root.toggleButtonDisable(false);

        return;
      }

      // this.$validator.validateAll().then((result) => {
      //   if (result) {
          this.$http[this.massActionValue.method.toLowerCase()](
            this.massActionValue.action,
            {
              rows: this.selectedTableRows,
              value: this.massActionOptionValue,
            }
          )
            .then((response) => {
              this.$root.pageLoaded = true;

              eventBus.emit("refresh_table_data", {
                usePrevious: true,
              });

              this.selectAllRows(true);

              this.massActionValue = "NA";
              this.massActionOptionValue = "NA";

              this.$root.toggleButtonDisable(false);
              successNotify(response.data.message);    
            })
            .catch((error) => {
              this.$root.pageLoaded = true;

              this.$root.toggleButtonDisable(false);
              errorNotify(error?.response?.data?.message);   
            });
        // } else {
        //   this.$root.pageLoaded = true;

        //   this.$root.toggleButtonDisable(false);

        //   eventBus.emit("onFormError");
        // }
      // });
    },

    changeDateRange: function (event) {
      this.custom_filter = event;
    },

    applyCustomFilter: function () {
      if (this.custom_filter[0] && this.custom_filter[1]) {
        let data = {
          cond: "bw",
          key: "duration",
          value: `${this.custom_filter[0]},${this.custom_filter[1]}`,
        };

        this.updateFilter(data);
      }
      this.toggleCustomTabFilter(false);
    },

    removeFilter: function (filter) {
      for (let index in this.myFilters) {
        if (
          this.myFilters[index].column === filter.column &&
          this.myFilters[index].cond === filter.cond &&
          this.myFilters[index].val === filter.val
        ) {
          if (this.myFilters[index].column == "perPage") {
            this.perPage = "10";
          }

          if (this.myFilters[index].column == "duration") {
            let key = "duration";

            let specificRangeDiv = document.querySelector(`#dateRange${key}`);

            let datePickers = specificRangeDiv.querySelectorAll(".flatpickr-input");

            datePickers.forEach((datePicker) => {
              let fp = datePicker._flatpickr;

              fp.set("minDate", "");

              fp.set("maxDate", "");
            });

            datePickers.value = "";
          }

          this.myFilters.splice(index, 1);

          this.makeURL();
        }
      }
    },

    updateFilterValue: function () {
      let allFilter = this.myFilters.filter((filter) => filter.val === "all");

      if (allFilter.length > 0) {
        let viewType =
          this.myFilters.find((filter) => filter.column === "view_type") ?? false;

        if (viewType) {
          allFilter.push(viewType);
        }

        this.myFilters = this.generateNewFilters(allFilter);
      } else {
        let otherFilters = this.myFilters.filter((filter) => filter.val !== "all");

        this.myFilters = this.generateNewFilters(otherFilters);
      }
    },

    generateNewFilters: function (filters) {
      return filters.map((filter) => {
        let column = this.columns.find(
          (column) => column.index == filter.column
        );

        if (column?.dropdown_options) {
          let value = filter.val.split(",");
          value = value.map(
            (id) => column.dropdown_options.find((option) => option.value == id).label
          );
          filter.prettyValue = value.join(",");
        }

        return filter;
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.filter-right {
  label {
    text-wrap: nowrap !important;
    margin-bottom: 0px;
    margin-right: 5px;
  }
  button {
    text-wrap: nowrap !important;
    i {
      margin-right: 0px !important;
    }
  }
}
.search-icon {
  height: max-content;
  font-size: 24px;
}
.search-close-icon {
  height: max-content;
  font-size: 18px;
  position: absolute;
  top: 0;
  right: 13px;
}
</style>

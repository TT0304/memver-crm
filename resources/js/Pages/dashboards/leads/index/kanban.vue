<script setup>
import { onMounted, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import { getCurrentDateString, getEndMonthDateString } from "@/common/utils";

import animationData from "@/Components/widgets/json_data/msoeawqm.json";
import animationData1 from "@/Components/widgets/json_data/gsqxdxog.json";
import { getLeadsData } from "@/http/lead/index.js";
const leads_data = ref({
  blocks: [],
  stage_names: [],
  stages: [],
});
const blocks_table_data = ref([]);
const { props: pageProps } = usePage();
const searchQuery = ref("");
const filterDateFrom = ref(null);
const filterDateTo = ref(null);

const startPicker = ref(null);
const endPicker = ref(null);
const leadDisplayMode = ref(1); //1: layout , 2: table
const currencyCode = pageProps.currencyCode;
// Define getLeads function
const getLeads = (searchedKeyword = "", filterValues = "") => {
  getLeadsData(searchedKeyword, filterValues, pageProps.pipelineId, leadDisplayMode.value)
    .then((leads) => {
      if (leadDisplayMode.value == 1) {
        leads_data.value = leads;
      } else if (leadDisplayMode.value == 2) {
        blocks_table_data.value = leads;
      }
    })
    .catch((error) => {
      console.log(error);
    });
};

const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

const debouncedGetLeads = debounce(getLeads, 2000);

const getTotalValueOfBlocks = (stage) => {
  let total_price = 0;
  const blocks = getBlocksByStage(stage.id);
  if (blocks && blocks.length > 0) {
    blocks.forEach((lead) => {      
      if (lead.stage.id == stage.id) total_price += parseFloat(lead.lead_value.replace(currencyCode, ''));
    });
  }
  return total_price == 0 ? "" : "$" + total_price.toFixed(2);
};
const getBlocksByStage = (stageId) => {
  if (!stageId || leads_data.value.blocks.length == 0) return [];
  return leads_data.value.blocks.filter((item) => item.stage.id == stageId);
};
const dragMove = (event) => {
  const previousStageId = event.from.getAttribute("datastageid");
  const newStageId = event.to.getAttribute("datastageid");
  const blocks = getBlocksByStage(previousStageId);
  const movedLead = blocks[event.oldIndex];
  const newStage = leads_data.value.stages.find((stage) => stage.id == newStageId);
  movedLead.stage = newStage;
  //dashboards.leads.update
  axios
    .put(route("dashboards.leads.update", movedLead.id), {
      lead_pipeline_stage_id: newStageId,
    })
    .then((res) => {
      // console.log(res);
    });
};

const updatePickerOptions = () => {
  if (startPicker.value && startPicker.value.fp) {
    startPicker.value.fp.set("maxDate", filterDateTo.value);
  }
  if (endPicker.value && endPicker.value.fp) {
    endPicker.value.fp.set("minDate", filterDateFrom.value);
  }
  if (filterDateFrom.value != null || filterDateTo.value != null) {
    const filterValue = `created_at[bw]=${
      filterDateFrom.value == null ? getCurrentDateString() : filterDateFrom.value
    },${filterDateTo.value == null ? getEndMonthDateString() : filterDateTo.value}`;
    getLeads(searchQuery.value, filterValue);
  }
};

const resetFilter = () => {
  filterDateFrom.value = null;
  filterDateTo.value = null;
};

const changeDisplayMode = (leadMode) => {
  leadDisplayMode.value = leadMode;
  getLeads();
};

// Call getLeads when component is mounted/created
onMounted(() => {
  getLeads();
});

watch(searchQuery, (newValue) => {
  debouncedGetLeads(newValue);
});

// Watchers to react to changes in filterDateFrom and filterDateTo
watch(filterDateFrom, (newValue, oldValue) => {
  if (newValue !== oldValue) {
    updatePickerOptions();
  }
});

watch(filterDateTo, (newValue, oldValue) => {
  if (newValue !== oldValue) {
    updatePickerOptions();
  }
});
</script>

<script>
import { Head, Link } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import { VueDraggableNext } from "vue-draggable-next";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

import Lottie from "@/Components/widgets/lottie.vue";
import ViewSwitch from "@/Pages/dashboards/leads/index/view_switch.vue";
import simplebar from "simplebar-vue";

export default {
  data() {
    return {
      pipelineType: "Default Pipeline",
      pipelines: ["Default Pipeline"],
      defaultOptions: {
        animationData: animationData,
      },
      defaultOptions1: {
        animationData: animationData1,
      },
      showFilterWidget: false,
    };
  },
  watch: {},
  methods: {
    toggleFilterWidget() {
      this.showFilterWidget = !this.showFilterWidget;
    },
  },
  components: {
    Layout,
    PageHeader,
    draggable: VueDraggableNext,
    lottie: Lottie,
    flatPickr,
    ViewSwitch,
    simplebar,
    Head,
    Link,
  },
};
</script>

<template>
  <BCard no-body>
    <BCardBody>
      <BRow class="g-2">
        <BCol v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('lead_create')" lg="auto">
          <div class="hstack gap-2">
            <BButton variant="primary"
              ><i class="ri-add-line align-bottom me-1"></i>
              <Link href="/leads/create" class="text-reset"
                >{{ $t("leads.create-title") }}
              </Link>
            </BButton>
          </div>
        </BCol>
        <BCol lg="3" class="col-auto">
          <div class="search-box">
            <input
              type="text"
              class="form-control search"
              :placeholder="$t('ui.datagrid.search')"
              v-model="searchQuery"
              @keyup.enter="getLeads(searchQuery)"
            />
            <i class="ri-search-line search-icon"></i>
            <i
              v-if="searchQuery"
              class="ri ri-close-line fs-24 search-close-icon"
              @click="searchQuery = ''"
            ></i>
          </div>
        </BCol>
        <div class="col-auto ms-sm-auto">
          <view-switch/>
         
          <BButton
            variant="primary"
            class="btn-suffix-label waves-effect waves-light lead-dispaly-mode"
            @click="toggleFilterWidget"
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
      </BRow>
      <BRow> </BRow>
    </BCardBody>
  </BCard>

  <div class="tasks-board mb-3" id="kanbanboard" v-if="leadDisplayMode == 1">
    <div class="tasks-list" v-for="stage in leads_data.stages" :key="stage">
      <div class="task-header">
        <div class="d-flex mb-2">
          <div class="flex-grow-1">
            <span class="fs-18 fw-semibold pipeline-title mb-0"> {{ stage.name }}</span>
          </div>
          <div class="flex-shrink-0">
            <span style="color: #53c41a; font-size: 18px">{{
              getTotalValueOfBlocks(stage)
            }}</span>
          </div>
        </div>

        <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('lead_create')" class="my-2">
          <Link
            :href="'/leads/create?stage_id=' + stage.id"
            class="btn btn-soft-info btn-create-lead w-100 fs-18 text-reset"
          >
            {{ $t("leads.create-title") }}
          </Link>
        </div>
      </div>

      <simplebar data-simplebar class="tasks-wrapper px-3 mx-n3">
        <div class="tasks">
          <draggable
            :list="getBlocksByStage(stage.id)"
            class="dragArea"
            group="test"
            @end="dragMove"
            :dataStageId="stage.id"
          >
            <BCard
              no-body
              class="tasks-box"
              v-for="(lead, index) of getBlocksByStage(stage.id)"
              :key="index"
            >
              <BCardBody>
                <div class="d-flex mb-2 align-items-center">
                  <h6 class="fs-15 mb-0 flex-grow-1 text-truncate task-title">
                    <a href="apps-tasks-details" class="d-block">{{ lead.title }}</a>
                  </h6>
                  <div class="view-drag">
                    <Link v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('lead_show')" :href="route('dashboards.leads.view', lead.id)" class="text-muted" aria-expanded="false"
                      ><i class="ri-eye-line fs-20"></i
                    ></Link>
                    <a href="javascript:void(0);" class="text-muted" aria-expanded="false"
                      ><i class="bx bx-crosshair fs-20"></i
                    ></a>
                  </div>
                </div>
                <div class="d-flex mb-2 align-items-center">
                  <i class="ri ri-user-line fs-24"></i>
                  <span class="fs-15 mx-2 lead-name">{{ lead.person_name }}</span>
                </div>
                <div class="d-flex mb-2 align-items-center">
                  <i class="ri ri-currency-fill fs-24"></i>
                  <span class="fs-15 mx-2">{{ lead.lead_value }}</span>
                </div>
              </BCardBody>
            </BCard>
          </draggable>
        </div>
      </simplebar>
    </div>
  </div>

  
  <BOffcanvas
    class="border-0"
    id="filter-settings-offcanvas"
    header-class="d-flex align-items-center bg-primary bg-gradient p-3"
    body-class="p-0"
    z-index="1005"
    footer-class="offcanvas-footer border-top p-3 text-center"
    placement="end"
    v-model="showFilterWidget"
  >
    <template #header>
      <div class="me-2">
        <h5 class="m-0 me-2 text-white">{{ $t("ui.datagrid.filter.title") }}</h5>
      </div>
      <button
        type="button"
        class="btn-close btn-close-white ms-auto"
        id="customizerclose-btn"
        @click="toggleFilterWidget"
      ></button>
    </template>
    <simplebar class="h-100">
      <div class="p-4">
        <h6 class="mb-3 fw-bold">{{ $t('datagrid.created_at') }}</h6>
        <div class="d-flex align-items-center">
          <div class="position-relative">
            <flat-pickr
              ref="startPicker"
              v-model="filterDateFrom"
              class="form-control flatpickr-input flatpickr-disabled"
            ></flat-pickr>
            <i
              class="ri-calendar-2-line fs-20 position-absolute"
              style="right: 10px; top: 50%; transform: translateY(-50%)"
            ></i>
          </div>
          <span class="mx-2">to</span>
          <div class="position-relative">
            <flat-pickr
              ref="endPicker"
              v-model="filterDateTo"
              class="form-control flatpickr-input flatpickr-disabled"
            ></flat-pickr>
            <i
              class="ri-calendar-2-line fs-20 position-absolute"
              style="right: 10px; top: 50%; transform: translateY(-50%)"
            ></i>
          </div>
        </div>
      </div>
    </simplebar>

    <template #footer>
      <BRow>
        <BCol cols="6">
          <BButton
            type="button"
            variant="light"
            class="w-100"
            id="reset-filter"
            @click="resetFilter()"
          >
          {{ $t('ui.datagrid.filter.reset') }}
          </BButton>
        </BCol>
        <BCol cols="6">
          <BButton type="button" variant="primary" class="w-100"> {{ $t('ui.datagrid.filter.preview') }} </BButton>
        </BCol>
      </BRow>
    </template>
  </BOffcanvas>
</template>

<style lang="scss">
.search-close-icon {
  position: absolute;
  top: 0px;
  bottom: 0px;
  right: 18px;
  display: flex;
  align-items: center;
  color: var(--vz-secondary-color);
}

.lead-dispaly-mode {
  margin-left: 10px;
}

.btn-lead-display-mode {
  margin-bottom: 0px;
  padding: 1px 12px;
}

.btn-suffix-label {
  position: relative;
  padding-right: 44px;

  .label-icon {
    position: absolute;
    width: 35.5px;
    right: calc(var(--vz-border-width) * -1);
    top: calc(var(--vz-border-width) * -1);
    bottom: calc(var(--vz-border-width) * -1);
    background-color: rgba(255, 255, 255, 0.1);
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 5px;
    margin-right: 0px !important;
  }
}

.flatpickr-calendar .flatpickr-day.flatpickr-disabled {
  background: #d3d3d3 !important;
  /* Light gray background */
  color: #a9a9a9 !important;
  /* Dark gray text */
  cursor: not-allowed;
}
.dragArea {
  min-height: 250px; /* Or any other suitable value */
  width: 100%; /* Ensure it covers the full width */
}
</style>

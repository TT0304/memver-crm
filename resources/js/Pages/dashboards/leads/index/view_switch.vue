<template>
  
    <BDropdown variant="light" @change="changePipeline(pl)" :text="pipelineName">
      <BDropdownItem
        v-for="(pl, index) in pipelines"
        :key="index"
        :value="pl.id"
        :selected="pipelineId == pl.id"
      >
        {{ pl.name }}
      </BDropdownItem>
    </BDropdown>
    <BButton-group
      role="group"
      aria-label="Basic radio toggle button group"
      class="lead-dispaly-mode"
    >
      <input
        type="radio"
        class="btn-check"
        name="btnradio"
        id="dispModeLayout"
        autocomplete="off"
        :checked="!view_type"
      />
      <Link
        class="btn btn-outline-secondary btn-lead-display-mode"
        for="dispModeLayout"
        :href="layoutUrl"
      >
        <i class="ri-layout-column-line fs-24"></i>
      </Link>

      <input
        type="radio"
        class="btn-check"
        name="btnradio"
        id="dispModeTable"
        autocomplete="off"
        :checked="view_type == 'table'"
      />
      <Link
        class="btn btn-outline-secondary btn-lead-display-mode"
        for="dispModeTable"
        :href="tableUrl"
      >
        <i class="ri-table-line fs-24"></i>
      </Link>
    </BButton-group>
</template>
<script setup>
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const { props } = usePage();
const { pipelines, pipelineId } = props;
const { curPiplineId } = ref();

const curPipeline = pipelines.filter((pipeline) => {
  return pipeline.id == pipelineId;
});

const urlParams = new URLSearchParams(window.location.search);
const view_type = urlParams.get("view_type");

const pipelineName = curPipeline && curPipeline.length > 0 ? curPipeline[0].name : "";

const changePipeline = (pl) => {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set("pipeline_id", pl.id);
  if (viewType) urlParams.set("view_type", viewType);

  return `${window.location.pathname}?${urlParams.toString()}`;
};

const layoutUrl = computed(() => {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.delete("view_type");
  return `${window.location.pathname}?${urlParams.toString()}`;
});

const tableUrl = computed(() => {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set("view_type", "table");
  return `${window.location.pathname}?${urlParams.toString()}`;
});
</script>
<script>
export default {
  data() {
    return {};
  },
  watch: {},
  methods: {},
  components: {},
};
</script>

<style lang="scss" scoped>
.lead-dispaly-mode {
  margin-left: 10px;
}

.btn-lead-display-mode {
  margin-bottom: 0px;
  padding: 1px 12px;
}
</style>

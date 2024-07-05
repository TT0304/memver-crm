<template>
  <div class="pipeline-stage-controls-wrapper" ref="stageWrapper">
    <div class="pipeline-stage-container">
      <ul class="pipeline-stages" :class="currentStage.code" ref="stageContainer">
        <template v-for="(stage, index) in customStages" :key="index">
          <li
            class="stage"
            :class="{ active: currentStage.sort_order >= stage.sort_order }"
            :title="stage.name"
            @click="changeStage(stage)"
            v-if="stage.code != 'won' && stage.code != 'lost'"
          >
            <span>{{ stage.name }}</span>
          </li>
        </template>

        <li class="stage">
          <span
            class="dropdown-toggle"
            @click="
              () => {
                showDropDown = !showDropDown;
              }
            "
          >
            {{ $t("leads.won-lost") }}
            <!-- <i class="ri-arrow-down-s-fill"></i> -->
          </span>

          <div class="dropdown-list" v-show="showDropDown">
            <div class="dropdown-container">
              <ul>
                <li
                  @click="
                    (e) => {
                      nextStageCode = won;
                      showModal = true;
                      console.log(nextStageCode);
                    }
                  "
                >
                  {{ $t("leads.won") }}
                </li>

                <li
                  @click="
                    (e) => {
                      nextStageCode = lost;
                      showModal = true;
                    }
                  "
                >
                  {{ $t("leads.lost") }}
                </li>
              </ul>
            </div>
          </div>
        </li>
      </ul>

      <div class="pipeline-stage-controls" v-if="showStageControl">
        <a class="btn btn-sm btn-secondary-outline" @click="scrollLeft">
          <i class="ri-arrow-left-s-line"></i>
        </a>

        <a class="btn btn-sm btn-secondary-outline" @click="scrollRight">
          <i class="ri-arrow-right-s-line"></i>
        </a>
      </div>
    </div>

    <div class="date-panel">
      <span class="pull-left">
        <i class="ri-calendar-2-line fs-20"></i>
        <label>{{ $t("leads.created-date:") }}</label>
        <span :title="formatDate(lead.created_at)">{{
          formatRelativeTime(lead.created_at)
        }}</span>
      </span>

      <span class="pull-right">
        <div v-if="lead.closed_at && ['won', 'lost'].indexOf(currentStage.code) >= 0">
          <i class="ri-calendar-2-line fs-20"></i>
          <label>{{ $t("leads.closed-date:") }}</label>
          <span :title="formatDate(lead.closed_at)">{{
            formatRelativeTime(lead.closed_at)
          }}</span>
        </div>

        <div v-else-if="lead.expected_close_date">
          <i class="ri-calendar-2-line fs-20"></i>
          <label>{{ $t("leads.expected-close-date:") }}</label>
          <span :title="formatDate(lead.expected_close_date)">
            {{
              isCloseDateToday(lead.expected_close_date)
                ? "Today"
                : formatRelativeTime(lead.expected_close_date)
            }}
          </span>
        </div>
      </span>
    </div>
  </div>

  <BModal
    v-model="showModal"
    hide-footer
    title-class="exampleModalLabel"
    header-class="bg-info-subtle p-3"
    class="v-modal-custom"
    centered
    :title="$t('leads.change-stage')"
  >
    <VForm
      :action="route('dashboards.leads.update', this.lead.id)"
      id="addform"
      class="tablelist-form"
      autocomplete="off"
      @submit="handleSubmit"
    >
      <VField name="_method" type="hidden" value="PUT" />
      <VField
        type="hidden"
        name="lead_pipeline_stage_id"
        :value="nextStageCode ? nextStageCode.id : 0"
      />
      <BRow class="g-3">
        <BCol lg="12" v-if="nextStageCode && nextStageCode.code == 'lost'">
          <div>
            <label for="lost_reason" class="form-label">{{
              $t("leads.lost-reason")
            }}</label>
            <VField as="textarea" class="form-control" name="lost_reason"></VField>
          </div>
        </BCol>
        <BCol lg="12" v-if="nextStageCode && nextStageCode.code == 'won'">
          <div>
            <label for="lead_value" class="form-label">{{ $t("leads.won-value") }}</label>
            <VField
              type="text"
              class="form-control"
              rules="required"
              name="lead_value"
              :value="lead.lead_value"
            />
          </div>
        </BCol>
        <BCol lg="12">
          <div>
            <label for="closed_at" class="form-label">{{
              $t("leads.closed-date")
            }}</label>
            <VField name="closed_at" v-model="closed_at">
              <flat-pickr
                name="closed_at"
                :modelValue="closed_at"
                @update:modelValue="(value) => (closed_at = value)"
                class="form-control flatpickr-input flatpickr-disabled"
              ></flat-pickr>
            </VField>
          </div>
        </BCol>
      </BRow>
      <div class="hstack gap-2 justify-content-end mt-3">
        <BButton type="button" variant="light" @click="showModal = false">Close</BButton>
        <BButton type="submit" variant="success" id="add-btn">
          {{ $t("leads.save-btn-title") }}
        </BButton>
      </div>
    </VForm>
  </BModal>
</template>

<script>
import axios from "axios";
import { useDateFormatter } from "@/common/utils";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { successNotify, errorNotify } from "@/common/toast";
import { useForm as useVeeForm } from "vee-validate";
const { formatDate, formatRelativeTime, isCloseDateToday } = useDateFormatter();
export default {
  props: ["lead", "lastCurrentStage", "customStages"],
  components: {
    flatPickr,
  },
  data() {
    return {
      currentStage: this.lastCurrentStage,
      nextStageCode: null,
      showStageControl: null,
      closed_at: null,
      showModal: false,
      showDropDown: false,
    };
  },
  computed: {
    won: function () {
      const results = this.customStages.filter((stage) => stage.code == "won");
      return results[0];
    },

    lost: function () {
      const results = this.customStages.filter((stage) => stage.code == "lost");
      return results[0];
    },
  },
  mounted() {
    var stagesWidht = this.customStages.length * 200;
    if (stagesWidht > this.$refs.stageWrapper.clientWidth) {
      this.showStageControl = true;
    }
  },
  watch: {},
  methods: {
    handleSubmit(values) {
      const self = this;
      values.lead_pipeline_stage_id = this.nextStageCode ? this.nextStageCode.id : 0;
      axios
        .put(route("dashboards.leads.update", this.lead.id), values)
        .then(function (response) {
          successNotify(response.data.message);
          self.currentStage = self.nextStageCode;
          self.showModal = false;
          self.showDropDown = false;
        })
        .catch(function (error) {
          errorNotify(error.response.data.message);
        });
    },
    changeStage: function (stage) {
      if (this.currentStage.code == stage.code) {
        return;
      }
      var self = this;
      axios
        .put(route("dashboards.leads.update", this.lead.id), {
          lead_pipeline_stage_id: stage.id,
        })
        .then(function (response) {
          self.currentStage = stage;
          successNotify(response.data.message);
        })
        .catch(function (error) {
          errorNotify(error.response.data.message);
        });
    },

    scrollLeft: function () {
      this.$refs.stageContainer.scrollLeft -= 200;
    },

    scrollRight: function () {
      this.$refs.stageContainer.scrollLeft += 200;
    },
    formatDate(dt) {
      return formatDate(dt);
    },
    formatRelativeTime(dt) {
      return formatRelativeTime(dt);
    },
    isCloseDateToday(dt) {
      return isCloseDateToday(dt);
    },
  },
};
</script>

<style lang="scss"></style>

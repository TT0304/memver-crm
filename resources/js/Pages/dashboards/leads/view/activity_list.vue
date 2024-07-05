<template>
  <div class="activity-list">
    <BTabs class="nav-tabs-custom mb-3" justified>
      <BTab
        v-for="type in types"
        :title="typeLabels[type]"
        :key="type"
        :selected="type == 'all'"
      >
        <div
          v-for="(subType, index) in ['planned', 'done']"
          :class="subType + '-activities ' + type"
          :key="index"
        >
          <div
            class="section-tag"
            v-if="type != 'note' && type != 'file' && type != 'email'"
          >
            <span v-if="subType == 'planned'">{{ $t("leads.planned") }}</span>

            <span v-else>{{ $t("leads.done") }}</span>

            <hr />
          </div>

          <div
            class="activity-item"
            v-for="(activity, index) in getActivities(type, subType)"
            :class="[activity.type == 'email' ? 'email' : 'activity']"
            :key="index"
          >
            <template v-if="activity.type != 'email'">
              <div class="title">
                <h4 v-if="activity.title">@{{ activity.title }}</h4>

                <span v-if="activity.type == 'note'">
                  {{ $t("leads.note-added") }}
                </span>

                <span v-else-if="activity.type == 'call'">
                  {{
                    `{!! ${$t("leads.call-scheduled", {
                      from: formatDate(activity.schedule_from),
                      to: formatDate(activity.schedule_to),
                    })} !!}`
                  }}
                </span>

                <span v-else-if="activity.type == 'meeting'">
                  {{
                    `{!! ${$t("leads.meeting-scheduled", {
                      from: formatDate(activity.schedule_from),
                      to: formatDate(activity.schedule_to),
                    })} !!}`
                  }}
                </span>

                <span v-else-if="activity.type == 'lunch'">
                  {{
                    `{!! ${$t("leads.lunch-scheduled", {
                      from: formatDate(activity.schedule_from),
                      to: formatDate(activity.schedule_to),
                    })} !!}`
                  }}
                </span>

                <span v-else-if="activity.type == 'file'">
                  {{ $t("leads.file-added") }}
                </span>

                <BDropdown
                  variant="transparent"
                  right
                  class="fw-medium text-reset mt-n2"
                  no-caret
                  style="position: absolute; right: 2px"
                >
                  <template #button-content>
                    <i class="mdi mdi-dots-vertical align-middle me-1 fs-20"></i>
                  </template>
                  <BDropdownItem v-if="!activity.is_done" @click="markAsDone(activity)">
                    {{ $t("leads.mark-as-done") }}
                  </BDropdownItem>
                  <BDropdownItem
                    :href="route('activities.edit', activity.id)"
                    target="_blank"
                  >
                    {{ $t("leads.edit") }}
                  </BDropdownItem>
                  <BDropdownItem @click="remove(activity)">
                    {{ $t("leads.remove") }}
                  </BDropdownItem>
                </BDropdown>
              </div>

              <div class="attachment" v-if="activity.file">
                <i class="ri-attachment-line"></i>

                <a
                  :href="route('activities.file_download', activity.file.id)"
                  target="_blank"
                >
                  @{{ activity.file.name }}
                </a>
              </div>

              <div
                class="comment"
                v-if="activity.comment"
                v-html="activity.comment"
              ></div>

              <div class="info">
                {{ activity.created_at ?? moment() }}

                <span class="seperator">·</span>

                <a :href="route('users.edit', activity.user.id)" target="_blank">
                  @{{ activity.user.name }}
                </a>
              </div>
            </template>

            <template v-else>
              <div class="subject">
                <h5>{{ activity.subject }}</h5>

                <span>
                  {{ activity.created_at ?? moment().format("Do MMM YYYY, h:mm A") }}

                  <span class="seperator">·</span>

                  @{{ activity.from }}

                  <span class="seperator">➝</span>

                  @{{ String(activity.reply_to) }}
                </span>

                <span class="icon ellipsis-icon dropdown-toggle"></span>
                <BDropdown
                  variant="transparent"
                  right
                  class="fw-medium text-reset mt-n2"
                  no-caret
                  style="position: absolute; right: 2px"
                >
                  <template #button-content>
                    <i class="mdi mdi-dots-vertical align-middle me-1 fs-20"></i>
                  </template>
                  <BDropdownItem
                    :href="
                      route('mail.view', {
                        route: activity.folders[0],
                        id: activity.id,
                      })
                    "
                    target="_blank"
                  >
                    {{ $t("leads.view") }}
                  </BDropdownItem>
                  <BDropdownItem @click="unlinkEmail(activity)">
                    {{ $t("leads.unlink") }}
                  </BDropdownItem>
                </BDropdown>
              </div>

              <div class="content">
                <div v-html="activity.reply"></div>
              </div>
            </template>
          </div>

          <div class="empty-activities" v-if="!getActivities(type, subType).length">
            <span v-if="subType == 'planned'">{{
              $t("leads.empty-planned-activities")
            }}</span>

            <span v-else>{{ $t("leads.empty-done-activities") }}</span>
          </div>
        </div>
      </BTab>
    </BTabs>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";
import eventBus from "@/common/eventBus";
import { useDateFormatter } from "@/common/utils.js";
import { successNotify, errorNotify } from "@/common/toast";
const { formatDate } = useDateFormatter();
export default {
  props: ["initialActivities", "quotes", "lead"],
  data() {
    return {
      types: ["all", "note", "call", "meeting", "lunch", "file", "email"],
      activities: this.initialActivities,
    };
  },
  mounted() {
    eventBus.on("ActivityUpdated", () => {
      axios
        .get(route("dashboards.leads.getActivities", { leadId: this.lead.id }))
        .then((res) => {
          this.activities = res.data.activities;
        });
    });
  },
  computed: {
    typeLabels: function () {
      return {
        all: this.$t("leads.all"),
        note: this.$t("leads.notes"),
        call: this.$t("leads.calls"),
        meeting: this.$t("leads.meetings"),
        lunch: this.$t("leads.lunches"),
        file: this.$t("leads.files"),
        email: this.$t("leads.emails"),
      };
    },
    all: function () {
      return this.activities;
    },
    note: function () {
      return this.activities.filter((activity) => activity.type == "note");
    },
    call: function () {
      return this.activities.filter((activity) => activity.type == "call");
    },
    meeting: function () {
      return this.activities.filter((activity) => activity.type == "meeting");
    },
    lunch: function () {
      return this.activities.filter((activity) => activity.type == "lunch");
    },
    email: function () {
      return this.activities.filter((activity) => activity.type == "email");
    },
    file: function () {
      return this.activities.filter((activity) => activity.type == "file");
    },
  },
  methods: {
    formatDate: (dtformat) => {
      return formatDate(dtformat);
    },
    getActivities: function (type, subType) {
      if (subType == "planned") {
        return this[type].filter((activity) => !activity.is_done);
      } else {
        return this[type].filter((activity) => activity.is_done);
      }
    },
    markAsDone: function (activity) {
      this.$root.pageLoaded = false;
      var self = this;
      axios
        .put(route("activities.update") + "/" + activity["id"], { is_done: 1 })
        .then(function (response) {
          self.$root.pageLoaded = true;
          activity.is_done = 1;
          successNotify(response.data.message);   
        })
        .catch(function (error) {
          self.$root.pageLoaded = true;
        });
    },
    remove: function (activity) {
      if (!confirm("Do you really want to perform this action?")) {
        return;
      }
      this.$root.pageLoaded = false;
      var self = this;
      axios
        .delete(route('activities.delete') + "/" + activity["id"])
        .then(function (response) {
          self.$root.pageLoaded = true;
          const index = self.activities.indexOf(activity);
          Vue.delete(self.activities, index);
          successNotify(response.data.message);   
        })
        .catch(function (error) {
          self.$root.pageLoaded = true;
        });
    },

    removeQuote: function (quote) {
      if (!confirm("Do you really want to perform this action?")) {
        return;
      }

      this.$root.pageLoaded = false;
      var self = this;
      axios
        .delete(route('leads.quotes.delete', $lead.id) + "/" + quote["id"])
        .then(function (response) {
          self.$root.pageLoaded = true;
          const index = self.quotes.indexOf(quote);
          self.quotes.slices(index, 1);
          successNotify(response.data.message);   
        })
        .catch(function (error) {
          self.$root.pageLoaded = true;
        });
    },
    moment() {
      return moment();
    },
    // formatDate: function (dateFormat) {
    //   return moment(String(date)).format(dateFormat);
    // },

    unlinkEmail: function (activity) {
      if (!confirm("Do you really want to perform this action?")) {
        return;
      }
      this.$root.pageLoaded = false;
      var self = this;
      var unlinkEmailid = activity.parent_id ? activity.parent_id : activity.id;
      axios
        .put(route("mail.update") + "/" + unlinkEmailid, {
          lead_id: null,
        })
        .then((response) => {
          self.$root.pageLoaded = true;
          var relatedActivities = self.activities.filter(
            (activityTemp) =>
              activityTemp.parent_id == unlinkEmailid || activityTemp.id == unlinkEmailid
          );
          relatedActivities.forEach(function (activity) {
            const index = self.activities.indexOf(activity);
            Vue.delete(self.activities, index);
          });
          successNotify(response.data.message);   
        })
        .catch((error) => {
          self.$root.pageLoaded = true;
        });
    },
  },
};
</script>

<style lang="scss"></style>

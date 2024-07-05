<template>
  <BCard>
    <BTabs nav-class="nav-tabs-custom nav-success mb-3" justified>
      <BTab :title="$t('leads.note')" active>
        <VForm
          method="post"
          label="note-form"
          v-slot="{ errors }"
          @submit="onSubmit($event, 'note-form', route('activities.store'))"
        >
          <VField type="hidden" name="type" value="note" />
          <VField type="hidden" name="lead_id" :value="lead.id" />
          <BRow class="g-3">
            <BCol lg="12">
              <label for="comment" class="form-label required">{{
                $t("leads.note")
              }}</label>
              <VField
                name="comment"
                rules="required"
                :label="$t('leads.note')"
                v-slot="{ field }"
              >
                <textarea
                  class="form-control"
                  name="comment"
                  v-bind="field"
                  style="min-height: 100px"
                  :state="errors['comment']"
                  :class="errors['comment'] ? 'is-invalid' : ''"
                ></textarea>
                <BFormInvalidFeedback v-if="errors['comment']">{{
                  errors["comment"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>
          <button type="submit" class="btn btn-md btn-primary mt-3">
            {{ $t("leads.save") }}
          </button>
        </VForm>
      </BTab>
      <BTab :title="$t('leads.activity')">
        <VForm
          :action="route('activities.store')"
          method="post"
          label="acitivity-form"
          @submit="onSubmit($event, 'activity-form', route('activities.store'))"
          v-slot="{ errors }"
        >
          <VField type="hidden" name="lead_id" :value="lead.id" />
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="title" class="form-label required">{{
                $t("leads.title-control")
              }}</label>
              <VField
                name="title"
                rules="required"
                :label="$t('leads.title-control')"
                v-slot="{ field }"
              >
                <BFormInput
                  class="form-control"
                  name="title"
                  v-bind="field"
                  :class="errors['title'] ? 'is-invalid' : ''"
                  :state="errors['title']"
                >
                </BFormInput>
                <BFormInvalidFeedback v-if="errors['title']">{{
                  errors["title"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>
          <BRow class="g-3 mb-3 mb-3">
            <BCol lg="12">
              <label for="type" class="form-label required">{{ $t("leads.type") }}</label>
              <VField
                name="type"
                rules="required"
                :label="$t('leads.type')"
                v-slot="{ field }"
              >
                <BFormSelect
                  name="type"
                  :class="errors['type'] ? 'is-invalid' : ''"
                  :state="errors['type']"
                  v-model="activity_type"
                  v-bind="field"
                >
                  <BFormSelectOption :value="null">
                    {{ $t("leads.select-type") }}</BFormSelectOption
                  >
                  <BFormSelectOption value="call">
                    {{ $t("leads.call") }}</BFormSelectOption
                  >
                  <BFormSelectOption value="meeting">
                    {{ $t("leads.meeting") }}</BFormSelectOption
                  >
                  <BFormSelectOption value="lunch">
                    {{ $t("leads.lunch") }}</BFormSelectOption
                  >
                </BFormSelect>
                <BFormInvalidFeedback v-if="errors['type']">{{
                  errors["type"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="schedule" class="form-label required">{{
                $t("leads.schedule")
              }}</label>
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative flex-grow-1">
                  <VField
                    v-model="schedule_from"
                    name="schedule_from"
                    :label="$t('leads.from')"
                    :rules="`required|date_format:YYYY-MM-DD HH:mm:ss|after:${moment().format(
                      'YYYY-MM-DD HH:mm:ss'
                    )}`"
                    v-slot="{ errors }"
                  >
                    <flat-pickr
                      ref="schedule_from"
                      v-model="schedule_from"
                      name="schedule_from"
                      :placeholder="$t('leads.from')"
                      :config="dateTimeConfig"
                      :class="errors.length > 0 ? 'is-invalid' : ''"
                      class="form-control flatpickr-input flatpickr-disabled"
                    ></flat-pickr>
                    <i
                      class="ri-calendar-2-line fs-20 position-absolute"
                      style="right: 10px; top: 50%; transform: translateY(-50%)"
                    ></i>

                    <BFormInvalidFeedback v-if="errors.length">{{
                      errors[0]
                    }}</BFormInvalidFeedback>
                  </VField>
                </div>
                <div class="position-relative flex-grow-1">
                  <VField
                    v-model="schedule_to"
                    name="schedule_to"
                    :label="$t('leads.to')"
                    :rules="`required|date_format:YYYY-MM-DD HH:mm:ss|after:${schedule_from}`"
                    v-slot="{ errors }"
                  >
                    <flat-pickr
                      ref="schedule_to"
                      v-model="schedule_to"
                      name="schedule_to"
                      :placeholder="$t('leads.from')"
                      :config="dateTimeConfig"
                      :class="errors.length > 0 ? 'is-invalid' : ''"
                      class="form-control flatpickr-input flatpickr-disabled"
                    ></flat-pickr>
                    <i
                      class="ri-calendar-2-line fs-20 position-absolute"
                      style="right: 10px; top: 50%; transform: translateY(-50%)"
                    ></i>

                    <BFormInvalidFeedback v-if="errors.length">{{
                      errors[0]
                    }}</BFormInvalidFeedback>
                  </VField>
                </div>
              </div>
            </BCol>
          </BRow>

          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="location" class="form-label">{{ $t("leads.location") }}</label>
              <VField name="location">
                <BFormInput class="form-control" name="location"> </BFormInput>
              </VField>
            </BCol>
          </BRow>

          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="description" class="form-label">{{
                $t("leads.description")
              }}</label>
              <VField name="description">
                <textarea class="form-control" name="description"> </textarea>
              </VField>
            </BCol>
          </BRow>

          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="participants" class="form-label">{{
                $t("leads.participants")
              }}</label>
              <div class="lookup-control">
                <div class="form-group">
                  <VField name="search_term" v-model="search_term">
                    <BFormInput
                      class="form-control"
                      name="location"
                      v-model="search_term"
                      autocomplete="off"
                      :placeholder="$t('leads.participant-info')"
                      @keyup="search"
                    >
                    </BFormInput>
                    <div class="lookup-results grouped" v-if="search_term.length">
                      <label>{{ $t("leads.users") }}</label>
                      <ul>
                        <li
                          v-for="(participant, index) in searched_participants.users"
                          :key="index"
                          @click="addParticipant('users', participant)"
                        >
                          <span>@{{ participant.name }}</span>
                        </li>

                        <li
                          v-if="
                            !searched_participants.users.length &&
                            search_term.length &&
                            !is_searching
                          "
                        >
                          <span>{{ $t("common.no-result-found") }}</span>
                        </li>
                      </ul>
                      <label>{{ $t("leads.persons") }}</label>
                      <ul>
                        <li
                          v-for="(participant, index) in searched_participants.persons"
                          @click="addParticipant('persons', participant)"
                          :key="index"
                        >
                          <span>@{{ participant.name }}</span>
                        </li>
                        <li
                          v-if="
                            !searched_participants.persons.length &&
                            search_term.length &&
                            !is_searching
                          "
                        >
                          <span>{{ $t("common.no-result-found") }}</span>
                        </li>
                      </ul>
                    </div>
                    <i class="icon loader-active-icon" v-if="is_searching"></i>
                  </VField>
                </div>
                <div class="lookup-selected-options">
                  <span
                    class="badge badge-sm badge-pill badge-secondary-outline users"
                    style="color: black;"
                    v-for="(participant, index) in participants.users"
                    :key="index"
                  >
                    <VField type="hidden" :name="`participants.users[${index}]`" :value="participant.id" />

                    {{ participant.name }}
                    <i
                      class="ri-close-line"
                      @click="removeParticipant('users', participant)"
                    ></i>
                  </span>

                  <span
                    class="badge badge-sm badge-pill badge-warning-outline persons"
                    style="color: black;"
                    v-for="(participant, index) in participants.persons"
                    :key="index"
                  >
                    <VField type="hidden" :name="`participants.persons[${index}]`" :value="participant.id" />

                    {{ participant.name }}
                    <i
                      class="ri-close-line"
                      @click="removeParticipant('persons', participant)"
                    ></i>
                  </span>
                </div>
              </div>
            </BCol>
          </BRow>
          <button type="submit" class="btn btn-md btn-primary">
            {{ $t("leads.save") }}
          </button>
        </VForm>
      </BTab>
      <BTab :title="$t('leads.email')">
        <VForm
          :action="route('mail.store')"
          method="post"
          label="email-form"
          @submit="onSubmit($event, 'email-form', route('mail.store'))"
          v-slot="{ errors }"
          enctype="multipart/form-data"
        >
          <VField type="hidden" name="lead_id" :value="lead.id" />
          <BRow class="g-3">
            <BCol lg="12" class="form-group email-control-group">
              <label for="comment" class="form-label required">{{
                $t("leads.to")
              }}</label>
              <email-tags-lead
                control-name="reply_to"
                :control-label="$t('leads.to')"
                :rules="'required'"
                :errors="errors"
              />
              <div class="email-address-options">
                <label @click="show_cc = !show_cc">
                  {{ $t("leads.cc") }}
                </label>

                <label @click="show_bcc = !show_bcc">
                  {{ $t("leads.bcc") }}
                </label>
              </div>
            </BCol>
          </BRow>
          <BRow class="g-3" v-if="show_cc">
            <BCol lg="12" class="form-group email-control-group">
              <label for="comment" class="form-label required">{{
                $t("leads.cc")
              }}</label>
              <email-tags-lead
                control-name="cc"
                :control-label="$t('leads.cc')"
                :rules="'required'"
                :errors="errors"
              />
            </BCol>
          </BRow>
          <BRow class="g-3" v-if="show_bcc">
            <BCol lg="12" class="form-group email-control-group">
              <label for="comment" class="form-label required">{{
                $t("leads.bcc")
              }}</label>
              <email-tags-lead
                control-name="bcc"
                :control-label="$t('leads.bcc')"
                :rules="'required'"
                :errors="errors"
              />
            </BCol>
          </BRow>
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="title" class="form-label required">{{
                $t("leads.subject")
              }}</label>
              <VField
                name="subject"
                rules="required"
                :label="$t('leads.subject')"
                v-slot="{ field }"
              >
                <BFormInput
                  class="form-control"
                  v-bind="field"
                  name="subject"
                  :class="errors['subject'] ? 'is-invalid' : ''"
                >
                </BFormInput>
                <BFormInvalidFeedback v-if="errors['subject']">{{
                  errors["subject"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="title" class="form-label required">{{
                $t("leads.reply")
              }}</label>
              <VField
                name="reply"
                rules="required"
                :label="$t('leads.reply')"
                v-model="editorData"
                v-slot="{ errors }"
                :class="errors['reply'] ? 'is-invalid' : ''"
              >
                <ckeditor v-model="editorData" :editor="editor"></ckeditor>
                <BFormInvalidFeedback v-if="errors['reply']">{{
                  errors["reply"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <attachment-wrapper/>
            </BCol>
          </BRow>
          <button type="submit" class="btn btn-md btn-primary">
            {{ $t("leads.send") }}
          </button>
        </VForm>
      </BTab>
      <BTab :title="$t('leads.file')">
        <VForm
          :action="route('activities.file_upload')"
          method="post"
          label="acitivity-form"
          @submit="onFileUpload($event)"
          v-slot="{ errors }"
        >
          <VField type="hidden" name="lead_id" :value="lead.id" />
          <VField type="hidden" name="type" value="file" />
          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="name" class="form-label">{{ $t("leads.name") }}</label>
              <VField name="name" v-slot="{ field }">
                <BFormInput class="form-control" name="name" v-bind="field"> </BFormInput>
              </VField>
            </BCol>
          </BRow>

          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="comment" class="form-label">{{
                $t("leads.description")
              }}</label>
              <VField name="comment" v-slot="{ field }">
                <textarea class="form-control" name="comment" v-bind="field"> </textarea>
              </VField>
            </BCol>
          </BRow>

          <BRow class="g-3 mb-3">
            <BCol lg="12">
              <label for="file" class="form-label required">{{ $t("leads.file") }}</label>
              <VField
                type="file"
                name="file"
                rules="required"
                class="form-control"
                :label="$t('leads.file')"
                v-slot="{ field }"
              >
                <input
                  class="form-control"
                  name="file"
                  type="file"
                  :state="errors['file']"
                  :class="errors['file'] ? 'is-invalid' : ''"
                  v-bind="field"
                />
                <BFormInvalidFeedback v-if="errors['file']">{{
                  errors["file"]
                }}</BFormInvalidFeedback>
              </VField>
            </BCol>
          </BRow>

          <button type="submit" class="btn btn-md btn-primary">
            {{ $t("leads.upload") }}
          </button>
        </VForm>
      </BTab>
      <BTab :title="$t('leads.quote')">
        <a href="/quotes/create" class="btn btn-md btn-primary" target="blank">
          {{ $t("leads.create-quote") }}
        </a>
      </BTab>
    </BTabs>
  </BCard>
</template>

<script>
import axios from "axios";
import moment from "moment";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import EmailTagsLead from "@/Components/attributes/edit/email_tags_lead.vue";
import AttachmentWrapper from "@/Components/attachment/attachment-wrapper.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import eventBus from "@/common/eventBus";
import { successNotify } from "@/common/toast";
export default {
  props: ["lead"],
  components: {
    flatPickr,
    EmailTagsLead,
    AttachmentWrapper,
    ckeditor: CKEditor.component,
  },
  data: function () {
    return {
      show_cc: false,
      show_bcc: false,
      schedule_from: null,
      schedule_to: null,
      search_term: "",
      activity_type: null,
      is_searching: false,
      editorData: "",
      uploadFile: null,
      editor: ClassicEditor,
      dateTimeConfig: {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S",
      },
      searched_participants: {
        users: [],
        persons: [],
      },
      participants: {
        users: [],
        persons: [],
      },
    };
  },
  setup() {},
  mounted: function () {
    var self = this;
    // tinymce.init({
    //   selector: 'textarea#reply',
    //   height: 200,
    //   width: "100%",
    //   plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
    //   toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
    //   image_advtab: true,
    //   setup: function (editor) {
    //     editor.on('keyUp', function () {
    //       self.$validator.validate('email-form.reply', this.getContent());
    //     });
    //   }
    // });
  },

  methods: {
    moment() {
      return moment();
    },
    onSubmit(values, submitType, url) {
      axios
        .post(url, {
          ...values,
        })
        .then((res) => {
          // notificationMethods.success('Updated Successfully')
          successNotify("Updated Successfully");
          eventBus.emit("ActivityUpdated");
        })
        .catch((error) => {});
    },
    async onFileUpload(values) {
      const formData = new FormData();
      Object.keys(values).forEach((key) => {
        formData.append(key, values[key]);
      });
      axios
        .post(route("activities.file_upload"), formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          // notificationMethods.success('Updated Successfully')
          successNotify("Updated Successfully");
          eventBus.emit("ActivityUpdated");
        })
        .catch((error) => {});
    },
    search() {
      const self = this;
      setTimeout(function () {
        self.is_searching = true;
        if (self.search_term.length < 2) {
          self.searched_participants = {
            users: [],
            persons: [],
          };
          self.is_searching = false;
          return;
        }

        self.$http
          .get(route('activities.search_participants') + '/?name=' + self.search_term)
          .then((response) => {
            ["users", "persons"].forEach(function (userType) {
              self.participants[userType].forEach(function (addedUser) {
                response.data[userType].forEach(function (user, index) {
                  if (user.id == addedUser.id) {
                    response.data[userType].splice(index, 1);
                  }
                });
              });
            });
            self.searched_participants = response.data;
            self.is_searching = false;
          })
          .catch((error) => {
            self.is_searching = false;
          });
      }, 500);
    },

    addParticipant: function (type, participant) {
      this.search_term = "";
      this.searched_participants = {
        users: [],
        persons: [],
      };
      this.participants[type].push(participant);
    },

    removeParticipant: function (type, participant) {
      const index = this.participants[type].indexOf(participant);
      this.participants[type].splice(index, 1);
    },

    checkIfOverlapping: function (e, formScope) {
      var self = this;
      this.$validator.validateAll(formScope).then(function (result) {
        if (result) {
          self.$http
            .post(route('activities.check_overlapping'), {
              schedule_from: self.schedule_from,
              schedule_to: self.schedule_to,
              participants: self.participants,
            })
            .then((response) => {
              if (!response.data.overlapping) {
                self.$root.onSubmit(e, formScope);
              } else {
                if (confirm($t('activities.duration-overlapping'))) {
                  self.$root.onSubmit(e, formScope);
                }
              }
            })
            .catch((error) => {});
        }
      });
    },
  },
};
</script>

<style lang="scss"></style>

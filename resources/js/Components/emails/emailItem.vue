<template>
  <div class="email-item">
    <div class="email-header">
      <div class="row">
        <span class="label">{{ $t('mail.from-') }}</span>
        <span class="value">{{ email.name + ' ' + email.from }}</span>
        <span class="time">
          <span>{{ timeAgo }}</span>
          <BDropdown variant="link" class="ms-sm-3 topbar-user" toggle-class="rounded-circle arrow-none" menu-class="dropdown-menu-end" :offset="{ alignmentAxis: -14, crossAxis: 0, mainAxis: 0 }">
            <template #button-content>
              <span class="icon ri-more-line"></span>
            </template>
            <ul style="list-style: none;">
              <li @mouseover="hovering = 'reply-white-icon'" @mouseout="hovering = ''" @click="emailAction('reply')">
                <i :class="['icon', 'ri-reply-line', { 'reply-white-icon': hovering === 'reply-white-icon' }]"></i>
                {{ $t('mail.reply') }}
              </li>

              <li @mouseover="hovering = 'reply-all-white-icon'" @mouseout="hovering = ''" @click="emailAction('reply-all')">
                <i :class="['icon', 'ri-reply-all-line', { 'reply-all-white-icon': hovering === 'reply-all-white-icon' }]"></i>
                {{ $t('mail.reply-all') }}
              </li>
              
              <li @mouseover="hovering = 'forward-white-icon'" @mouseout="hovering = ''" @click="emailAction('forward')">
                <i :class="['icon', 'ri-share-forward-line', { 'forward-white-icon': hovering === 'forward-white-icon' }]"></i>
                {{ $t('mail.forward') }}
              </li>
              
              <li @mouseover="hovering = 'trash-white-icon'" @mouseout="hovering = ''" @click="emailAction('delete')">
                  <i class="icon ri-delete-bin-5-line" @click="deleteEmail" :class="{'trash-white-icon': hovering == 'trash-white-icon'}"></i>
                  {{ $t('mail.delete') }}
              </li>
              <!-- Other action items -->
            </ul>
          </BDropdown>
        </span>
        <!-- Other email header rows -->
      </div>
      <div class="row">
          <span class="label">
              {{ $t('mail.to-') }}
          </span>

          <span class="value">
              {{ String(email.reply_to) }}
          </span>
      </div>
      
      <div class="row" v-if="email.cc && email.cc.length">
          <span class="label">
              {{ $t('mail.cc-') }}
          </span>

          <span class="value">
              @{{ String(email.cc) }}
          </span>
      </div>
      
      <div class="row" v-if="email.bcc && email.bcc.length">
          <span class="label">
              {{ $t('mail.bcc-') }}
          </span>

          <span class="value">
              @{{ String(email.bcc) }}
          </span>
      </div>
    </div>

    <div class="email-content">
      <div v-html="email.reply"></div>
      <div class="attachment-list">
        <div class="attachment-item" v-for="(attachment, index) in email.attachments" :key="index">
          <a :href="`${attachmentDownloadRoute}/${attachment.id}`">
            <i class="icon ri-attachment-line"></i>
            {{ attachment.name }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from "axios";
import { useTimeAgo } from '@vueuse/core'

export default {
  props: ['email'],
  setup(props) {
    const timeAgo = useTimeAgo(new Date(props.email.created_at));
    const hovering = ref('');
    return {
      hovering,
      timeAgo
    };
  },
  methods: {
    
    emailAction (type) {
      if (type !== 'delete') {
        // Emit event with email data
        const eventData = { type, email: this.email };
        // Assuming you've defined onEmailAction as an event listener in the parent component
        this.$emit('onEmailAction', eventData);
      } else {
        if (!window.confirm(this.$t("ui.datagrid.massaction.mass-delete-confirm"))) {
          return;
        }
        axios.delete(`/mail/${this.email.id}`)
        .then(response => {
          window.location.href = '/mail/outbox';
        })
        .catch(error => {
          console.error('Error deleting email', error);
        });
      }
    }

  },
  computed: {
    // locale() {
    //   return '{{ app()->getLocale() }}'; // Assuming you're passing the locale from the backend
    // },
    attachmentDownloadRoute() {
      return route("mail.attachment_download");
    },
  },
};
</script>

<style>
.email-item {
    margin-bottom: 40px;
}
.email-item .email-header .row .time {
    float: right;
    position: relative;
    text-align: right;
    width: 215px;
    font-size: 18px;
}
.email-item .email-header .row .label {
    font-weight: 400;
    font-size: 18px;
}
.email-item .email-header .row .value {
    font-weight: 500;
    font-size: 18px;
}
.email-item .email-header .row {
  display: block;
}
.email-item .email-header {
    margin-bottom: 20px;
}
.email-item .email-content {
    background-color: #fff;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    color: #546e7a;
    padding: 30px;
    font-size: 18px;
}
.email-list .email-action span.reply-button {
    margin-right: 20px;
}
.email-list .email-action span {
    color: #546e7a;
    cursor: pointer;
    font-weight: 500;
    padding: 8px;
    font-size: 18px;
}
.email-item .email-header .row .time ul {
    padding: 0px;
}
.email-item .email-header .row .time ul li {
    color: #546e7a;
    cursor: pointer;
    font-size: 16px;
    padding: 8px 12px;
}
.icon {
  margin-right: 5px;
  font-size: 18px;
}
</style>

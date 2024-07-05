<template>
  <div class="email-list">
    <!-- Display the main email -->
    <emailItem
      :email="email"
      @onEmailAction="emailAction"
    />

    <!-- Display replies -->
    <div class="email-reply-list">
      <emailItem
        v-for="(email, index) in email.emails"
        :email="email"
        @onEmailAction="emailAction"
      />
    </div>

    <!-- Show action buttons or email form -->
    <div class="email-action" v-if="!action">
      <span class="reply-button" @click="emailAction({ type: 'reply' })">
        <i class="icon ri-reply-line"></i>
        {{ $t('mail.reply') }}
      </span>
      <span class="forward-button" @click="emailAction({ type: 'forward' })">
        <i class="icon ri-share-forward-line"></i>
        {{ $t('mail.forward') }}
      </span>
    </div>
    <div class="email-form-container" v-else>
      <emailForm
        :action="action"
        @onDiscard="discard"
      />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import EmailItem from "@/Components/emails/emailItem.vue";
import EmailForm from "@/Components/emails/emailForm.vue";

export default {
  components: {
    EmailItem,
    EmailForm,
  },
  props: ["email"],
  setup(props) {
    const action = ref(null);
    
    const emailAction = (event) => {
      action.value = event;

      if (!action.value.email) {
        action.value.email = lastEmail();
      }

      setTimeout(() => {
        scrollBottom();
      }, 0);
    }
    const scrollBottom = () => {
      const scrollBottom = window.scrollY + window.innerHeight;
      window.scrollTo(0, scrollBottom);
    };

    const lastEmail = () => {
      if (!props.email.emails || !props.email.emails.length) {
        return props.email;
      }

      return props.email.emails[props.email.emails.length - 1];
    };

    const discard = () => {
      action.value = null;
    };

    return {
      emailAction,
      lastEmail,
      scrollBottom,
      discard,
      action
    };
  },
};
</script>

<style scoped>
/* Add your scoped styles here */
.icon {
    background-size: cover;
    display: inline-block;
    height: 20px;
    vertical-align: top;
    width: 20px;
}
</style>

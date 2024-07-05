<template>
  <form
    enctype="multipart/form-data"
    @submit.prevent="submit"
    ref="createForm"
  >
    <div class="form-container">

      <div class="mb-3">
        <label
          class="form-label"
          :class="{ required: 1 }"
        >
          {{ $t('leads.to') }}
        </label>
        <email_tags
          :attribute="{code:'reply_to', name: 'To'}"
          rules="required"
          :data="reply_to"
          @toggle-cc="toggleCc"
          @toggle-bcc="toggleBcc"
          :errors="errors"
        />
      </div>
      <div class="mb-3" v-if="show_cc">
        <label
          class="form-label"
        >
          {{ $t('leads.cc') }}
        </label>
        <email_tags
          :cc="show_cc"
          :attribute="{code:'cc', name: 'Cc'}"
          :data="cc?cc:null"
          @toggle-cc="toggleCc"
          @toggle-bcc="toggleBcc"
          :errors="errors"
        />
      </div>
      <div class="mb-3" v-if="show_bcc">
        <label
          class="form-label"
        >
          {{ $t('leads.bcc') }}
        </label>
        <email_tags
          :bcc="show_bcc"
          :attribute="{code:'bcc', name: 'Bcc'}"
          :data="bcc?bcc:null"
          @toggle-cc="toggleCc"
          @toggle-bcc="toggleBcc"
          :errors="errors"
        />
      </div>
      <div class="mb-3">
        <label
          class="form-label"
          :class="{ required: 1 }"
        >
          {{ $t('leads.reply') }}
        </label>
          
        <textarea_custom
          :attribute="{code:'reply', name: 'Reply'}"
          :data="reply"
          :errors="errors"
        />
      </div>

      <div class="form-group">
        <Attachment></Attachment>
      </div>

      <div class="d-flex panel-bottom">

        <div class="flex-shrink-0">
          <button type="submit" variant="primary" class="btn btn-primary">
            <i class="ri-send-plane-line align-bottom me-1"></i>
            {{ $t("mail.send") }}
          </button>
        </div>

        <div class="flex-shrink-0">
          <label @click="discard" class="btn btn-link waves-effect waves-light mx-2">{{ $t('mail.discard') }}</label>
        </div>
        
      </div>
    </div>
  </form>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import Email_tags from "@/Components/attributes/edit/email_tags.vue";
import Textarea_custom from "@/Components/attributes/edit/textarea_custom.vue";
import Attachment from "@/Components/attachment/attachment-wrapper.vue";
import { useForm as useVeeForm } from "vee-validate";
import { Head, Link, router, useForm } from "@inertiajs/vue3";

export default {
  props: ['action'],
  components: {
    Email_tags,
    Attachment,
    Textarea_custom
  },
  data() {
    return {
      show_cc: false,
      show_bcc: false
    };
  },
  setup(props) {
    const { setError, errors, handleSubmit } = useVeeForm();
    const reply_to = computed(() => {
      if (props.action.type == 'forward') {
        return [];
      }
      return [props.action.email.from];
    });

    const cc = computed(() => {
      if (props.action.type != 'reply-all') {
        return [];
      }

      return props.action.email.cc;
    });

    const bcc = computed(() => {
      if (props.action.type != 'reply-all') {
        return [];
      }

      return props.action.email.bcc;
    });

    const reply = ref('');

    const submit = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      
      const fileInputs = Array.from(document.querySelectorAll('input[name="attachments[]"]')); // Select the file input elements
      // fileInputs.map((input, key)=>{
      //   values['attachments['+key+']'] = input.files[0];
      // })
      let attachCount = 0;
      fileInputs.map((input, key)=>{
        if(input.files[0] == undefined){
          attachCount ++;
        }else{
          values['attachments['+ (key - attachCount) +']'] = input.files[0];
        }
      })

      form
        .transform((data) => ({
          ...values,
          entity_type: "emails",
          parent_id: props.action.email.id,
          is_draft: 0
        }))
        .post(route("mail.store"), {
          onSuccess: () => {
            window.location.href = '/main/inbox';
          },
          onError: (error) => {
            Object.keys(error).forEach((key) => {
              setFieldError(key, error[key]);
            });
          },
        });
    });
   
    return {
      reply_to,
      cc,
      bcc,
      reply,
      errors,
      submit,
    };
  },
  methods: {
    discard (){
      this.$emit('onDiscard');
    },
    toggleCc() {
      this.show_cc = !this.show_cc;
    },
    toggleBcc() {
      this.show_bcc = !this.show_bcc;
    }
  }
};
</script>

<style scoped>
/* Add your scoped styles here */
</style>

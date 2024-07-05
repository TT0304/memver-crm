
<script>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";
import AttributeEdit from "@/Components/attributes/edit.vue";
import { useStore } from "vuex";
import { successNotify, errorNotify } from "@/common/toast";

export default {
  data() {
    return {};
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const createFormData = ref({});
    const is_draft = ref(0);
    const show_cc = ref(false);
    const show_bcc = ref(false);

    const {
      currencyCode,
      customAttributes,
      email
    } = pageProps;
    const { setError, errors, handleSubmit } = useVeeForm();

    const sendDraft = (e, draft) => {
      e.preventDefault();
      e.stopPropagation();
      is_draft.value = draft;
      submit();
    }
    
    const submit = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      const fileInputs = Array.from(document.querySelectorAll('input[name="attachments[]"]')); // Select the file input elements
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
          is_draft: is_draft.value
        }))
        .post((email ? route('mail.update', email.id) : route('mail.store')), {
          onSuccess: (response) => {
            successNotify(response.data.message);    
            window.location.href = email ? '/main/draft' : '/main/inbox';
          },
          onError: (error) => {
            Object.keys(error).forEach((key) => {
              setFieldError(key, error[key]);
            });
            errorNotify(error.response.data.message);
          },
        });
    });

    return {
      createFormData,
      errors,
      currencyCode,
      customAttributes,
      submit,
      is_draft,
      show_cc,
      show_bcc,
      sendDraft,
      email
    };
  },

  computed: {},
  mounted: () => {
    
  },
  methods: {
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
    AttributeEdit,
  },
};
</script>


<template>
  <Layout>
    <Head :title="$t('mail.compose')" />
    <PageHeader :title="$t('mail.compose')" :pageTitle="$t('layouts.mail.title')" />

    <form @submit="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_create')" class="flex-shrink-0">
                    <button type="submit" @click="(e)=>sendDraft(e, 0)" variant="primary" class="btn btn-primary">
                      <i class="ri-send-plane-line align-bottom me-1"></i>
                      {{ $t("mail.send") }}
                    </button>
                  </div>

                  <input type="hidden" name="is_draft" v-model="is_draft" />
                  <input type="hidden" name="id" :value="email ? email.id : ''" />

                  <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_create')" class="flex-shrink-0">
                    <input type="submit" @click="(e)=>sendDraft(e, 1)" :value="$t('mail.save-to-draft')" class="btn btn-link waves-effect waves-light mx-2" />
                  </div>
                  <div class="flex-shrink-0">
                    <Link
                      :href="route('mail.index')"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("mail.back") }}
                    </Link>
                  </div>
                </BCardHeader>
                <AttributeEdit
                  :customAttributes="customAttributes"
                  :currencyCode="currencyCode"
                  :data="email"
                  :errors="errors"
                />
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </form>
  </Layout>
</template>

<style></style>

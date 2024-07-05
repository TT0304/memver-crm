
<script>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";
import AttributeEdit from "@/Components/attributes/edit.vue";
import SelectEdit from "@/Components/attributes/edit/select.vue";
import { useStore } from "vuex";
import { successNotify, errorNotify } from "@/common/toast";

export default {
  data() {
    return {};
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const createFormData = ref({});
    const {
      currencyCode,
      customAttributes,
      user,
    } = pageProps;

    const { setError, errors, handleSubmit } = useVeeForm();
    const store = useStore();
    
    const submit = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      form
        .transform((data) => ({
          ...values,
        }))
        .post(user ? route('users.update', user.id) : route('users.store'), {
          onSuccess: (response) => {
            successNotify(response.data.message);    
            // window.location.href = '/email-templates';
          },
          onError: (error) => {
            Object.keys(error).forEach((key) => {
              setFieldError(key, error[key]);
            });
          },
        });
    });
    
    return {
      createFormData,
      errors,
      currencyCode,
      customAttributes,
      submit,
      user,
    };
  },

  computed: {},
  mounted: () => {},
  methods: {
    
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
    AttributeEdit,
    SelectEdit
  },
};
</script>

<template>
  <Layout>
    <Head :title="user ? $t('settings.users.edit-title') : $t('settings.users.create-title')" />
    <PageHeader :title="user ? $t('settings.users.edit-title') : $t('settings.users.create-title')" :pageTitle="$t('settings.users.title')" />

    <form @submit="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('user_create')" class="flex-shrink-0">
                    <button type="submit" variant="primary" class="btn btn-primary">
                      <i class="ri-add-line align-bottom me-1"></i>
                      {{ user ? $t('settings.users.update-btn-title') : $t("settings.users.save-btn-title") }}
                    </button>
                  </div>
                  <div class="flex-shrink-0">
                    <a
                      :href="route('users.index')"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("settings.email-templates.back") }}
                  </a>
                  </div>
                </BCardHeader>
                <AttributeEdit
                  :customAttributes="customAttributes"
                  :currencyCode="currencyCode"
                  :data="user"       
                  :errors="errors"
                />
                <div class="mb-3">
                  <label class="form-label">{{ $t("settings.roles.title") }}</label>
                  <SelectEdit
                    :attribute="{
                      code: 'roles',
                      name: 'Role',
                      lookup_type: 'roles'
                    }"
                    :data="user ? user.roles[0]?.id : null"
                  />
                </div>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </form>
  </Layout>
</template>

<style></style>

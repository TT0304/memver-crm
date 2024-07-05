
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
    const {
      currencyCode,
      customAttributes,
      person
    } = pageProps;
    const { setError, errors, handleSubmit } = useVeeForm();
    const store = useStore();
    
    const submit = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      form
        .transform((data) => ({
          ...values,
        }))
        .post((person ? route('contacts.persons.update', person.id) : route('contacts.persons.store')), {
          onSuccess: (response) => {
            successNotify(response.data.message);    
            window.location.href = '/contacts/persons';
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
      person,
      submit,
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
  },
};
</script>

<template>
  <Layout>
    <Head :title="person&&person.id? $t('contacts.persons.edit-title') : $t('contacts.persons.create-title')" />
    <PageHeader :title="person&&person.id? $t('contacts.persons.edit-title') : $t('contacts.persons.create-title')" :pageTitle="$t('contacts.persons.title')" />

    <form @submit="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('person_create')" class="flex-shrink-0">
                    <button type="submit" variant="primary" class="btn btn-primary">
                      <i class="ri-add-line align-bottom me-1"></i>
                      {{ $t("contacts.persons.save-btn-title") }}
                    </button>
                  </div>
                  <div class="flex-shrink-0">
                    <a
                      :href="route('contacts.persons.index')"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("contacts.persons.back") }}
                  </a>
                  </div>
                </BCardHeader>
                <AttributeEdit
                  :customAttributes="customAttributes"
                  :currencyCode="currencyCode"
                  :errors="errors"
                  :data="person"
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

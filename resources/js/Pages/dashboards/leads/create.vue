<script>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";

import ProductList from "@/Pages/dashboards/leads/common/products.vue";
import AttributeEdit from "@/Components/attributes/edit.vue";
import ContactInput from "@/Pages/dashboards/leads/common/contact.vue";

export default {
  data() {
    return {
      detail_info: {},
      person_info: null,
      products: [],
    };
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const req_stage_id = ref(pageProps.stage_id);
    const createFormData = ref({});
    const currencyCode = pageProps.currencyCode;
    const { setError, errors, handleSubmit } = useVeeForm();
    
    const submit = handleSubmit((values, { setFieldError }) => {
      values.lead_pipeline_stage_id = req_stage_id.value;
      const form = useForm(values);
      form
        .transform((data) => ({
          ...values,
          entity_type: "leads",
          status: 1,
          user_id: 1,
          lead_pipeline_id: 1,
        }))
        .post(route("dashboards.leads.store"), {
          onSuccess: () => {
            // window.location.href = '/apps/leads';
          },
          onError: (error) => {
            Object.keys(error).forEach((key) => {
              setFieldError(key, error[key]);
            });
          },
        });
    });
    return {
      req_stage_id,
      createFormData,
      errors,
      currencyCode,
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
    ProductList,
    AttributeEdit,
    ContactInput,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('leads.create-title')" />
    <PageHeader :title="$t('leads.create-title')" :pageTitle="$t('leads.title')" />

    <form @submit="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div class="flex-shrink-0">
                    <button type="submit" variant="primary" class="btn btn-primary">
                      <i class="ri-add-line align-bottom me-1"></i>
                      {{ $t("leads.save-btn-title") }}
                    </button>
                  </div>
                  <div class="flex-shrink-0">
                    <a
                      href="/leads"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("leads.back") }}
                    </a>
                  </div>
                </BCardHeader>
                <BTabs
                  nav-class="nav-border-top nav-border-top-primary mb-3"
                  content-class="text-muted"
                >
                  <BTab :title="$t('leads.details')" active>
                    <div class="live-preview p-3">
                      <AttributeEdit
                        :customAttributes="$page.props.customAttributes"
                        :currencyCode="currencyCode"
                        :errors="errors"
                      />
                    </div>
                  </BTab>
<BTab :title="$t('leads.contact-person')">
                    <div class="live-preview p-3">
                      <ContactInput
                        :data="person_info"
                        :organizationAttribute="$page.props.organizationAttribute"
                      ></ContactInput>
                    </div>
                  </BTab>
                  <BTab :title="$t('leads.products')">
                    <div class="live-preview p-3">
                      <ProductList :data="products"></ProductList>
                    </div>
                  </BTab>
                </BTabs>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </form>
  </Layout>
</template>

<style></style>

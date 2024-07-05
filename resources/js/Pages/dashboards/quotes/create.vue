
<script>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";
import LookupInput from "@/Components/attributes/edit/lookup.vue";
import AttributeEdit from "@/Components/attributes/edit.vue";
import QuoteList from "@/Components/quotes/quote-list.vue";
import { useStore } from "vuex";

export default {
  data() {
    return {};
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const createFormData = ref({});
    const {
      lookUpEntityData,
      lead,
      customAttributes1,
      customAttributes2,
      customValidations,
      entity,
      currencyCode,
      groupStates,
      countries,
    } = pageProps;
    const { setError, errors, handleSubmit } = useVeeForm();
    const store = useStore();
    // Example of dispatching an action directly
    const updateStatesCountries = () => store.dispatch("data/updateStatesByCountries", groupStates);
    const updateCountries = () => store.dispatch("data/updateCountries", countries);
    console.log(pageProps);
    
    const submit = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      form
        .transform((data) => ({
          ...values,
        }))
        .post(route("quotes.store"), {
          onSuccess: () => {
            window.location.href = '/quotes';
          },
          onError: (error) => {
            Object.keys(error).forEach((key) => {
              setFieldError(key, error[key]);
            });
          },
        });
    });
    onMounted(() => {
      updateStatesCountries();
      updateCountries();
    })
    
    return {
      createFormData,
      errors,
      currencyCode,
      lead,
      customValidations,
      lookUpEntityData,
      customAttributes1,
      customAttributes2,
      entity,
      currencyCode,
      groupStates,
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
    LookupInput,
    QuoteList,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('quotes.create-title')" />
    <PageHeader :title="$t('quotes.create-title')" :pageTitle="$t('quotes.title')" />

    <form @submit="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('quote_create')" class="flex-shrink-0">
                    <button type="submit" variant="primary" class="btn btn-primary">
                      <i class="ri-add-line align-bottom me-1"></i>
                      {{ $t("quotes.save-btn-title") }}
                    </button>
                  </div>
                  <div class="flex-shrink-0">
                    <a
                      href="/quotes"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("quotes.back") }}
                    </a>
                  </div>
                </BCardHeader>
                <BAccordion>
                  <BAccordionItem :title="$t('quotes.quote-information')" visible>
                    <AttributeEdit
                      :customAttributes="customAttributes1"
                      :currencyCode="currencyCode"
                      :errors="errors"
                      :customValidations="customValidations"
                    />
                    <div class="mb-3">
                      <label class="form-label">{{ $t("quotes.lead") }}</label>
                      <LookupInput
                        :attribute="{
                          code: 'lead_id',
                          name: 'Lead',
                          lookup_type: 'leads',
                        }"
                        :data="lookUpEntityData"
                      ></LookupInput>
                    </div>
                  </BAccordionItem>
                </BAccordion>
                <BAccordion>
                  <BAccordionItem :title="$t('quotes.address-information')" visible>
                    <AttributeEdit
                      :customAttributes="customAttributes2"
                      :currencyCode="currencyCode"
                      :errors="errors"
                    />
                  </BAccordionItem>
                </BAccordion>
                <BAccordion>
                  <BAccordionItem :title="$t('quotes.quote-items')" visible>
                    <quote-list
                      :currencyCode="currencyCode"
                      :errors="errors"/>
                  </BAccordionItem>
                </BAccordion>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </form>
  </Layout>
</template>

<style></style>

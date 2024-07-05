<script>
import { Head, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import EmailItem from "@/Components/emails/emailItem.vue";
import EmailForm from "@/Components/emails/emailForm.vue";
import EmailAction from "@/Components/emails/emailAction.vue";
import EmailList from "@/Components/emails/emailList.vue";
import Modal from "@/Components/modal.vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";
import { mapState, mapActions } from "vuex";
import { ref, onMounted } from "vue";
import { successNotify, errorNotify } from "@/common/toast";

import ProductList from "@/Pages/dashboards/leads/common/products.vue";
import AttributeEdit from "@/Components/attributes/edit.vue";
import ContactInput from "@/Pages/dashboards/leads/common/contact.vue";

export default {
  data() {
    return {
      products: [],
      person_info: null,
      route1: window.location.pathname.split('/').pop(),
      route2: window.location.pathname.split('/')[window.location.pathname.split('/').length - 2],
    };
  },
  props: ['email', 'currencyCode', 'customAttributes', 'organizationAttribute', 'organizationAttributeone', 'html'],
  setup(props) {
    const { setError, errors, handleSubmit } = useVeeForm();
    const is_Person = ref(0);
    
    const savePerson = (e, isperson) => {
      e.preventDefault();
      e.stopPropagation();
      is_Person.value = isperson;
      submitPerson();
    }

    const submitPerson = handleSubmit((values, { setFieldError }) => {
      const form = useForm(values);
      console.log(form)
      form
        .transform((data) => ({
          ...values,
        }))
        .post((is_Person.value ? route('contacts.persons.store') : route('dashboards.leads.store')), {
          onSuccess: (response) => {
            successNotify(response.data.message);    
            // window.location.href = email ? '/main/draft' : '/main/inbox';
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
      errors,
      submitPerson,
      savePerson
    };
  },
  methods: {
    ...mapActions("data", ["toggleSidebarFilter"]),
  },
  components: {
    EmailItem,
    EmailForm,
    EmailAction,
    EmailList,
    Modal,
    ProductList,
    AttributeEdit,
    ContactInput,
    Layout,
    Head,
    PageHeader
  },
};
</script>
<template >
  <Layout>
    <Head :title="$t('layouts.mail.title') + '/' + $t('layouts.mail.' + route2)" />
    <PageHeader :title="route1" :pageTitle="$t('layouts.mail.title') + '/' + $t('layouts.mail.' + route2)" />
    <BContainer>
      <BRow class="justify-content-center">
        <BCard no-body>
          <BCardHeader class="align-items-center d-flex border-bottom-0 justify-content-end">
            <div class="flex-shrink-0 w-100">
              <div class="linkbtn-group">
                <BButton variant="primary" class="btn-suffix-label" @click="toggleSidebarFilter">
                  <div class="" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas"
                    aria-controls="theme-settings-offcanvas" id="mdi-cog1"></div>
                  <i class="ri-links-line label-icon align-middle fs-16 me-2"></i>
                  {{ $t("mail.link-mail") }}
                </BButton>
              </div>
              <emailAction :email="email" :html="html" />
              <emailList
                :email="email"
              />
            </div>
            <form @submit.prevent="submitPerson">
              <modal :is-open="$root.modalIds.addPersonModal"
                @update:isOpen="isOpen => $root.modalIds.addPersonModal = isOpen">
                <template #header>
                  <h3>{{ $t('contacts.persons.create-title') }}</h3>
                  <div class="header-actions">
                    <button class="btn btn-sm btn-secondary-outline" @click="$root.closeModal('addPersonModal')">
                      {{ $t('contacts.persons.cancel') }}
                    </button>
                    <button type="submit" @click="(e)=>savePerson(e, 1)" class="btn btn-sm btn-primary">
                      {{ $t('contacts.persons.save-btn-title') }}
                    </button>
                  </div>
                </template>
                <template #body>
                  <input type="hidden" name="email_id" :value="email.id" />
                  <input type="hidden" name="quick_add" value="1" />
                  <AttributeEdit
                    :customAttributes="organizationAttribute"
                    :errors="errors"
                  />
                </template>
              </modal>
            </form>
            <form  @submit.prevent="submitPerson" >
              <modal id="addLeadModal" :is-open="$root.modalIds.addLeadModal">
                <template #header>
                  <h3>{{ $t('leads.create-title') }}</h3>
                  <div slot="header-actions">
                    <button class="btn btn-sm btn-secondary-outline" @click="$root.closeModal('addLeadModal')">
                      {{ $t('leads.cancel') }}
                    </button>
                    <button type="submit" @click="(e)=>savePerson(e, 0)" class="btn btn-sm btn-primary">
                      {{ $t('leads.save-btn-title') }}
                    </button>
                  </div>
                </template>
                <template #body>
                  <div slot="body" style="padding: 0">
                    <input type="hidden" name="email_id" :value="email.id" />
                    <input type="hidden" name="quick_add" value="1" />
                    <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="1" />
                    <BTabs
                      nav-class="nav-border-top nav-border-top-primary mb-3"
                      content-class="text-muted"
                    >
                      <BTab :title="$t('leads.details')" active>
                        <div class="live-preview p-3">
                          <AttributeEdit
                            :customAttributes="customAttributes"
                            :currencyCode="currencyCode"
                            :errors="errors"
                          />
                        </div>
                      </BTab>
                      <BTab :title="$t('leads.contact-person')">
                        <div class="live-preview p-3">
                          <ContactInput
                            :data="person_info"
                            :organizationAttribute="organizationAttributeone"
                          ></ContactInput>
                        </div>
                      </BTab>
                      <BTab :title="$t('leads.products')">
                        <div class="live-preview p-3">
                          <ProductList :data="products" ></ProductList>
                        </div>
                      </BTab>
                    </BTabs>
                  </div>
                </template>
              </modal>
            </form>
          </BCardHeader>
          <BCardBody>
          </BCardBody>
        </BCard>
      </BRow>
    </BContainer>
  </Layout>
</template>

<style lang="scss">
  .linkbtn-group{
    margin-bottom: 10px;
    text-align: end;
  }
</style>

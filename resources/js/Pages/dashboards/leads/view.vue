<template>
  <Layout>
    <Head :title="lead.title" />
    <PageHeader :title="$t('leads.title')" :pageTitle="$t('dashboard.title')" />
    <div class="krain-content-container">
      <div class="content full-page">
        <div class="page-header">
          <div class="page-title">
            <h1 class="fs-24">
              {{ lead.title }}
              <tags-component :lead="lead" />
            </h1>
          </div>

          <div v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('lead_edit')" class="page-action">
            <button class="btn btn-primary btn-md" @click="displayUpdateModal(true)">
              Edit
            </button>
          </div>
        </div>
        <div class="page-content lead-view">
          <div class="lead-content-left">
            <BCard>
              <BCardHeader style="padding: 5px 0px">
                <h4>{{ $t("leads.details") }}</h4>

                <div v-if="lead.rotten_days > 0">
                  <span class="lead-rotten-info">
                    <i class="ri-alert-line"></i>
                    {{ $t("leads.rotten-info", { days: lead.rotten_days }) }}
                  </span>
                </div>
              </BCardHeader>
              <BCardBody style="padding: 10px 0px !important">
                <div class="custom-attribute-view">
                  <AttributeView :customAttributes="customAttributes" :entity="lead">
                  </AttributeView>

                  <div class="attribute-value-row" v-if="lead.stage?.code == 'lost'">
                    <div class="label">{{ $t("leads.lost-reason") }}</div>
                    <div class="value">{{ lead.lost_reason }}</div>
                  </div>
                </div>
              </BCardBody>
            </BCard>

            <BCard>
              <BCardHeader style="padding: 5px 0px">
                <h4>{{ $t("leads.contact-person") }}</h4>

                <div v-if="lead.rotten_days > 0">
                  <span class="lead-rotten-info">
                    <i class="ri-alert-line"></i>
                    {{ $t("leads.rotten-info", { days: lead.rotten_days }) }}
                  </span>
                </div>
              </BCardHeader>
              <BCardBody style="padding: 10px 0px !important">
                <div class="custom-attribute-view">
                  <div class="attribute-value-row">
                    <div class="label">Name</div>
                    <div class="value">
                      <a
                        :href="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('person_edit') && route('contacts.persons.edit', person?.id)"
                        target="_blank"
                      >
                        {{ person.name ?? "" }}
                      </a>
                    </div>
                  </div>

                  <div class="attribute-value-row">
                    <div class="label">Email</div>
                    <div class="value">
                      <email-view :value="person.emails ?? []"></email-view>
                    </div>
                  </div>

                  <div class="attribute-value-row">
                    <div class="label">Contact Numbers</div>

                    <div class="value">
                      <phone-view :value="person.contact_numbers ?? []"></phone-view>
                    </div>
                  </div>

                  <div class="attribute-value-row">
                    <div class="label">Organization</div>

                    <div class="value">
                      <a
                        :href="
                          $page.props.auth.user.permissions?.map((pr)=>pr.name).includes('organization_edit') &&
                          route('contacts.organizations.edit', personOrganization.id)
                        "
                        target="_blank"
                        v-if="personOrganization"
                      >
                        {{ personOrganization.name }}
                      </a>
                      <span v-else>
                        {{ $t("common.not-available") }}
                      </span>
                    </div>
                  </div>
                </div>
              </BCardBody>
            </BCard>

            <BCard>
              <BCardHeader style="padding: 5px 0px">
                <h4>{{ $t("leads.products") }}</h4>
              </BCardHeader>

              <BCardBody style="padding: 10px 0px !important">
                <div class="lead-product-list" v-if="products && products.length > 0">
                  <div
                    class="lead-product"
                    v-for="(product, index) in products"
                    :key="index"
                  >
                    <div class="top-control-group">
                      <div class="form-group">
                        <label>{{ $t("leads.item") }}</label>

                        <div class="control-faker">
                          {{ product.name }}
                        </div>
                      </div>
                    </div>

                    <div class="bottom-control-group" style="padding-right: 0">
                      <div class="form-group">
                        <label>{{ $t("leads.price") }}</label>

                        <div class="control-faker">
                          {{ product.price }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label>{{ $t("leads.quantity") }}</label>

                        <div class="control-faker">
                          {{ product.quantity }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label>{{ $t("leads.amount") }}</label>

                        <div class="control-faker">
                          {{ product.price * product.quantity }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="empty-record">
                  <img src="@assets/images/empty-table-icon.svg" />

                  <span>{{ $t("common.no-records-found") }}</span>
                </div>
              </BCardBody>
            </BCard>
          </div>
          <div class="lead-content-right">
            <stage-component
              :lastCurrentStage="currentStage"
              :customStages="customStages"
              :lead="lead"
            />
            <activity-action :lead="lead" />
            <activity-list
              :initialActivities="activities"
              :quotes="quotes"
              :lead="lead"
            />
          </div>
        </div>
      </div>
    </div>

    <BModal
      v-model="showModal"
      hide-footer
      title-class="exampleModalLabel"
      header-class="bg-info-subtle p-3"
      class="v-modal-custom"
      centered
      :title="$t('leads.edit-title')"
    >
      <form
        @submit="submit"
        class="row g-3"
        ref="createForm"
        enctype="multipart/form-data"
      >
        <VField
          type="hidden"
          id="lead_pipeline_stage_id"
          name="lead_pipeline_stage_id"
          :value="lead.lead_pipeline_stage_id"
        />
        <BTabs
          nav-class="nav-border-top nav-border-top-primary mb-3"
          content-class="text-muted"
        >
          <BTab :title="$t('leads.details')" active>
            <div class="live-preview p-3">
              <AttributeEdit
                :customAttributes="$page.props.customAttributes"
                :currencyCode="currencyCode"
                :data="lead"
                :errors="updateErrors"
              />
            </div>
          </BTab>
          <BTab :title="$t('leads.contact-person')">
            <div class="live-preview p-3">
              <ContactInput
                :data="lead.person"
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
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" @click="displayUpdateModal(false)">{{
            $t("leads.cancel")
          }}</BButton>
          <BButton type="submit" variant="success" id="add-btn">
            {{ $t("leads.save-btn-title") }}
          </BButton>
        </div>
      </form>
    </BModal>
  </Layout>
</template>

<script>
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { usePage } from "@inertiajs/vue3";
import { notificationMethods } from "@/state/helpers";
import TagsComponent from "@/Pages/dashboards/leads/view/tags.vue";
import StageComponent from "@/Pages/dashboards/leads/view/stage.vue";
import ActivityAction from "@/Pages/dashboards/leads/view/activity_action.vue";
import ActivityList from "@/Pages/dashboards/leads/view/activity_list.vue";
import AttributeView from "@/Components/attributes/view.vue";
import EmailView from "@/Components/attributes/view/email.vue";
import PhoneView from "@/Components/attributes/view/phone.vue";
import LookupView from "@/Components/attributes/view/lookup.vue";

import { useForm as useVeeForm } from "vee-validate";
import ProductList from "@/Pages/dashboards/leads/common/products.vue";
import AttributeEdit from "@/Components/attributes/edit.vue";
import ContactInput from "@/Pages/dashboards/leads/common/contact.vue";
import { successNotify, errorNotify } from "@/common/toast";
import axios from "axios";

export default {
  setup() {
    const { props: pageProps } = usePage();
    const { lead } = pageProps;
    const { customAttributes } = pageProps;
    const { person } = pageProps;
    const { personOrganization } = pageProps;
    const { products } = pageProps;
    const { customStages } = pageProps;
    const { currentStage } = pageProps;
    const { activities } = pageProps;
    const { quotes } = pageProps;
    const { currencyCode } = pageProps;
    const showModal = ref(false);

    const { setError, errors: updateErrors, handleSubmit } = useVeeForm();

    const submit = handleSubmit((values, { setFieldError }) => {
      
      axios
        .put(route("dashboards.leads.update", lead.id), values)
        .then(function (response) {
          showModal.value = false;
          successNotify(response.data.message);      
          window.location.href = route('dashboards.leads.view', lead.id);
        })
        .catch(function (error) {
          Object.keys(error).forEach((key) => {
            setFieldError(key, error[key]);
          });
          errorNotify(error.response.data.message);
        });
        // const form = useForm(values);
        // form
        //   .transform((data) => ({
        //     ...values
        //   }))
        //   .put(route("dashboards.leads.update", lead.id), {
        //     onSuccess: () => {
        //       // window.location.href = '/apps/leads';
        //     },
        //     onError: (error) => {
        //       Object.keys(error).forEach((key) => {
        //         setFieldError(key, error[key]);
        //       });
        //     },
        //   });
    });

    const displayUpdateModal = (val) => {
      showModal.value = val;
    };
    return {
      lead,
      customAttributes,
      person,
      products,
      personOrganization,
      currentStage,
      customStages,
      activities,
      quotes,
      updateErrors,
      currencyCode,
      showModal,
      submit,
      displayUpdateModal,
    };
  },
  watch: {},
  methods: {},
  components: {
    Layout,
    PageHeader,
    Head,
    TagsComponent,
    AttributeView,
    EmailView,
    PhoneView,
    LookupView,
    StageComponent,
    ActivityAction,
    ActivityList,
    ProductList,
    AttributeEdit,
    ContactInput,
  },
};
</script>

<style lang="scss"></style>

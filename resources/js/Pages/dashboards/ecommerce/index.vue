<script>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import TableComponent from "@/Components/datagrid/table.vue";
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import { usePage } from "@inertiajs/vue3";

export default {
  data() {
    return {};
  },
  setup() {
    const { props: pageProps } = usePage();
    const {
      totalLeads,
      todayLeads,
      weeklyLeads,
      labels,
      leadsPerPipeline,
    } = pageProps;

    const pieChart = {
      type: "pie",
      data: {
        labels: labels,
        datasets: [
          {
            backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#DD1B16"],
            data: leadsPerPipeline,
          },
        ],
      },
    };

    return {
      totalLeads,
      todayLeads,
      weeklyLeads,
      leadsPerPipeline,
      pieChart,
    };
  },
  watch: {},
  mounted(){
    console.log(this.labels);
  },
  methods: {},
  components: {
    Layout,
    PageHeader,
    Head,
    TableComponent,
    Vue3ChartJs,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('t-dashboards')" />
    <PageHeader :title="$t('t-dashboards')" :pageTitle="$t('t-dashboards')" />
    <BCard no-body>
      <BCardBody>
        <BRow class="m-2 justify-content-between">
          <BCol class="mt-2" col sm="12" lg="4">
            <div class="card m-auto p-4" style="background: #ff7373; font-size: 20px;">Total Leads: {{ totalLeads }}</div>
          </BCol>
          <BCol class="mt-2" col sm="12" lg="4">
            <div class="card m-auto p-4" style="background: #c5ff73; font-size: 20px;">Today's Leads: {{ todayLeads }}</div>
          </BCol>
          <BCol class="mt-2" col sm="12" lg="4">
            <div class="card m-auto p-4" style="background: #7373ff; font-size: 20px;">Weekly Leads: {{ weeklyLeads }}</div>
          </BCol>
        </BRow>
        <BRow class="m-2">
          <BCol col sm="12" lg="6">
            <div class="card p-2">
              <div class="m-auto"><h5>Leads per Pipline</h5></div>
              <div class="m-auto" style="max-width: 100%;">
                <vue3-chart-js v-bind="{ ...pieChart }" />
              </div>
            </div>
          </BCol>
        </BRow>
      </BCardBody>
    </BCard>
  </Layout>
</template>
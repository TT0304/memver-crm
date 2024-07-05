<script setup>
const urlParams = new URLSearchParams(window.location.search);
const view_type = urlParams.get("view_type");
</script>

<script>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import TableView from "@/Pages/dashboards/leads/index/table_view.vue";
import Kanban from "@/Pages/dashboards/leads/index/kanban.vue";

export default {
  data() {
    return {};
  },
  watch: {},
  methods: {},  
  components: {
    Layout,
    PageHeader,
    Head,
    TableView,
    Kanban
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('leads.title')" />
    <PageHeader :title="$t('leads.title')" :pageTitle="$t('dashboard.title')" />
    <div v-if="view_type == 'table'">
      <TableView />
    </div>
    <div v-else>
      <Kanban/>
    </div>
  </Layout>
</template>

<style lang="scss"></style>

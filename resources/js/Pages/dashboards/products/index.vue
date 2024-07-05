<script setup>
const urlParams = new URLSearchParams(window.location.search);
const view_type = urlParams.get("view_type");
</script>

<script>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import TableComponent from "@/Components/datagrid/table.vue";

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
    TableComponent,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('products.title')" />
    <PageHeader :title="$t('products.title')" :pageTitle="$t('dashboard.title')" />
    <TableComponent :dataSrc="route('products.index')">
      <template v-slot:table-header>
        <h1></h1>
      </template>
      <template v-slot:table-action>
        <a v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('product_create')" :href="route('products.create')" class="btn btn-md btn-primary">{{
          $t("products.create-title")
        }}</a>
      </template>
    </TableComponent>
  </Layout>
</template>

<style lang="scss"></style>

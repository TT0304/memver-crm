<script>
import { Head, Link } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import simplebar from "simplebar-vue"

export default {
  data() {
    return {
      
    };
  },
  watch: {
  },
  methods: {
    
  },
  components: {
    Layout,
    PageHeader,
    simplebar,
    Head, Link,
  },
};
</script>

<template>
  <Layout>

    <Head title="Leads Board" />
    <PageHeader title="Leads" pageTitle="Dashboards" />

    <BCard no-body>
      <BCardBody>
        <BRow class="g-2">
          <BCol lg="auto">
            <div class="hstack gap-2">

              <BButton variant="primary"><i class="ri-add-line align-bottom me-1"></i>
                <Link href="/leads/create" class="text-reset">Create Lead
                </Link>
              </BButton>
            </div>
          </BCol>
          <BCol lg="3" class="col-auto">
            <div class="search-box">
              <input type="text" class="form-control search" :placeholder="$t('ui.datagrid.search')" v-model="searchQuery"
                @keyup.enter="getLeads(searchQuery)">
              <i class="ri-search-line search-icon"></i>
              <i v-if="searchQuery" class="ri ri-close-line fs-24 search-close-icon" @click="searchQuery = ''"></i>
            </div>
          </BCol>
          <div class="col-auto ms-sm-auto">
            <BDropdown :text="pipelineType" variant="light" id="dropdownPipeline">
              <!-- <BDropdownItem v-for="(pl, index) in pipelines" :key="index">
                {{ pl }}
              </BDropdownItem> -->
            </BDropdown>
            
          </div>
        </BRow>
        <BRow>

        </BRow>
      </BCardBody>
    </BCard>

    
  </Layout>

</template>

<style lang="scss">
</style>
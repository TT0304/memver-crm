<script>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import TableComponent from "@/Components/datagrid/table.vue";
import { usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { successNotify, errorNotify } from "@/common/toast";

export default {
  data() {
    return {};
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const createFormData = ref({});
    const {
      roles,
      canEditRole,
      canShowRole,
      canDeleteRole,
      canCreateRole,
    } = pageProps;

    return{
      roles,
      canEditRole,
      canShowRole,
      canDeleteRole,
      canCreateRole,
    };

  },
  watch: {},
  methods: {
    doAction: function ({ event, route, method, confirm_text }) {
      if (confirm_text) {
        if (confirm(confirm_text)) {
          this.performAjax({ event, route, method });
        }
      } else {
        this.performAjax({ event, route, method, type: "download" });
      }
    },
    performAjax: function ({ event, route, method, type }) {
      const self = this;
      this.$http[method.toLowerCase()](route)
      .then((response) => {
        event.preventDefault();
        console.log(response.data.message);
        successNotify(response.data.message);
        window.location.reload();
      })
      .catch((error) => {
        event.preventDefault();
        errorNotify(error?.response?.data?.message);
        window.location.reload();
      });
    },
  },
  components: {
    Layout,
    PageHeader,
    Head,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('t-settings')" />
    <PageHeader :title="$t('settings.users.role')" :pageTitle="$t('t-settings')" />
    <BCard no-body>
      <BCardBody>
        <BRow class="g-2">
          <div
            v-for="role in roles"
            :key="role.id"
            class="col-md-4 mb-3"
          >
            <div class="card" style="background: #d6d6d6a8;">
              <div class="card-body">
                <h5 class="card-title">Role Name: {{ role.name }}</h5>
                <p class="card-text">Total Permissions: {{ role.permissions.length }}</p>
                <div class="text-center">
                  <a
                    v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_edit')"
                    :href="'/settings/roles/edit/' + role.id"
                    class="btn btn-sm btn-success m-2"
                  >
                    {{ $t("settings.roles.edit") }}
                  </a>
                  <a
                    v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_show')"
                    :href="'/settings/roles/show/' + role.id"
                    class="btn btn-sm btn-info m-2"
                  >
                    {{ $t("settings.roles.view") }}
                  </a>
                  <button
                    v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_delete')"
                    class="btn btn-sm btn-danger m-2"
                    @click="
                      doAction({
                        event: $event,
                        route: `/settings/roles/${role.id}`,
                        method: 'delete',
                        confirm_text: $t('ui.datagrid.massaction.delete'),
                      })
                    "
                  >
                    {{ $t("settings.roles.delete") }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card" style="background: #d6d6d6a8;">
              <div class="card-body">
                <a
                  v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_create')"
                  href="/settings/roles/create"
                  class="btn btn-success"
                >
                  {{ $t("settings.roles.add") }}
                </a>
              </div>
            </div>
          </div>
        </BRow>
      </BCardBody>
    </BCard>
  </Layout>
</template>

<style lang="scss"></style>

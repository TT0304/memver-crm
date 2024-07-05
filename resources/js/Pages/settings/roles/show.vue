<script>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useForm as useVeeForm } from "vee-validate";
import { useStore } from "vuex";
import { successNotify, errorNotify } from "@/common/toast";
import { computed } from 'vue';

export default {
  data() {
    return {
    };
  },
  setup(props) {
    const { props: pageProps } = usePage();
    const {
      role,
    } = pageProps;

    const sortedPermissions = computed(() => {
      return role.permissions.sort((a, b) => a.name.localeCompare(b.name));
    });

    return {
      role,
      permissions: sortedPermissions,
    };
  },
  mounted: () => {},
  methods: {
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
  },
};
</script>

<template>
  <Layout>
    <Head :title="$t('settings.roles.show-title')" />
    <PageHeader :title="$t('settings.roles.show-title')" :pageTitle="$t('settings.roles.title')" />

      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div class="flex-shrink-0">
                    <a
                      :href="route('roles.index')"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("settings.email-templates.back") }}
                  </a>
                  </div>
                </BCardHeader>
                    <!-- Role Name -->
                    <div>
                      <label class="form-label" for="name">
                        {{ $t('settings.roles.name') }}
                      </label>
                      <label class="form-check-label">
                        : {{ role.name }}
                      </label>
                    </div>

                    <!-- Permissions -->
                    <div>
                      <label class="form-label">{{ $t('settings.roles.permissions') }}:</label>
                   
                      <div class="row">
                        <div
                          v-for="(permission, index) in permissions"
                          :key="permission.id"
                          class="col-md-4 col-sm-6 col-lg-4"
                        >
                          <div class="form-check form-switch">
                            <!-- {{ index }}: -->
                            <label class="form-check-label p-1" style="border: 1px solid grey;width: 100%;">
                              {{ permission.name }}
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
  </Layout>
</template>

<style></style>

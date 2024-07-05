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
    const createFormData = ref({});
    const {
      permissions,
      role,
    } = pageProps;

    const sortedPermissions = computed(() => {
      return permissions.sort((a, b) => a.name.localeCompare(b.name));
    });

    const formData = ref({
      name: role?role.name:'',
      permissions: role?role.permissions.map((p) => p.id):[]
    });
    
    const { setError, errors, handleSubmit } = useVeeForm();
    const submit = handleSubmit(() => {
      axios.post(
        role ? route('roles.update', role.id) : route('roles.store'),
        formData.value // Use `.value` to access the object within `ref`
      )
      .then((response) => {
        successNotify(response.data.message);
        window.location.href = '/settings/roles';
      })
      .catch((error) => {
        console.error("Error:", error); // Check for errors
        errorNotify(error.response?.data?.message || "Submission failed");
      });
    });
    
    return {
      createFormData,
      errors,
      submit,
      role,
      permissions:sortedPermissions,
      formData
    };
  },
  computed: {},
  mounted: () => {},
  methods: {
    selectAll() {
      this.formData.permissions = this.permissions.map((p) => p.id);
    },
    deselectAll() {
      this.formData.permissions = [];
    }
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
    <Head :title="role ? $t('settings.roles.edit-title') : $t('settings.roles.create-title')" />
    <PageHeader :title="role ? $t('settings.roles.edit-title') : $t('settings.roles.create-title')" :pageTitle="$t('settings.roles.title')" />

    <form @submit.prevent="submit" class="row g-3" ref="createForm" enctype="multipart/form-data">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol col lg="10">
            <BCard no-body>
              <BCardBody>
                <BCardHeader class="align-items-center d-flex border-bottom-0">
                  <BCardTitle class="mb-0 flex-grow-1"></BCardTitle>
                  <div class="flex-shrink-0">
                    <button v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_create')" type="submit" variant="primary" class="btn btn-primary">
                      <i class="ri-add-line align-bottom me-1"></i>
                      {{ role ? $t("settings.roles.update-btn-title") : $t("settings.roles.save-btn-title") }}
                    </button>
                  </div>
                  <div class="flex-shrink-0">
                    <a
                      :href="route('roles.index')"
                      class="btn btn-link waves-effect waves-light mx-2"
                    >
                      {{ $t("settings.email-templates.back") }}
                  </a>
                  </div>
                </BCardHeader>
                    <input type="hidden" name="guard_name" value="web"/>

                    <!-- Role Name -->
                    <div>
                      <label class="form-label required" for="name">
                        {{ $t('settings.roles.name') }}
                      </label>
                      <input
                        class="form-control"
                        type="text"
                        name="name"
                        for="name"
                        id="name"
                        v-model="formData.name"
                        required
                      />
                    </div>

                    <!-- Permissions -->
                    <div>
                      <label class="form-label required">{{ $t('settings.roles.permissions') }}</label>
                      <div style="padding-bottom: 4px">
                        <button type="button" class="btn btn-info btn-xs mx-2" @click="selectAll">
                          {{ $t('settings.roles.select_all') }}
                        </button>
                        <button type="button" class="btn btn-info btn-xs mx-2" @click="deselectAll">
                          {{ $t('settings.roles.deselect_all') }}
                        </button>
                      </div>

                      <div class="row">
                        <div
                          v-for="(permission, index) in permissions"
                          :key="permission.id"
                          class="col-md-4 col-sm-6 col-lg-4"
                        >
                          <div class="form-check form-switch">
                            <input
                              class="form-check-input switch"
                              type="checkbox"
                              name="permissions[]"
                              :id="'permission_' + permission.id"
                              v-model="formData.permissions"
                              :value="permission.id"
                            />
                            <label :name="'permission_' + permission.id" :for="'permission_' + permission.id" class="form-check-label">
                              {{ permission.name }}
                            </label>
                          </div>
                        </div>
                      </div>
                      <div v-if="errors.permissions" class="invalid-feedback">
                        {{ errors.permissions }}
                      </div>
                    </div>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BContainer>
    </form>
  </Layout>
</template>

<style></style>

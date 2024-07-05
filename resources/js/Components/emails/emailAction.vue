<template>
  <BOffcanvas
    class="border-0 sidebar-filter"
    id="filter-settings-offcanvas"
    header-class="d-flex align-items-center bg-primary bg-gradient p-3"
    body-class="p-0"
    z-index="1005"
    footer-class="offcanvas-footer border-top p-3 text-center"
    placement="end"
    v-model="sidebarFilter"
  >
    <template #header>
      <div class="me-2">
        <h5 class="m-0 me-2 text-white">{{ $t("mail.link-mail") }}</h5>
      </div>
      <div class="right">
        <button
          type="button"
          class="btn-close btn-close-white ms-auto"
          id="customizerclose-btn"
          @click="toggleSidebarFilter"
        ></button>
      </div>
    </template>
    <div class="email-action-content">
      <!-- View Render Event before linking a person -->
      <!-- Linking a Person -->
      <div class="link-lead" v-if="email == undefined || !email.person_id">
        <h3>{{ $t('mail.link-mail') }}</h3>

        <div class="btn-group">
          <button
            class="btn btn-sm btn-primary-outline"
            @click="enabled_search.person = true"
            v-if="! enabled_search.person"
            >
            {{ $t('mail.add-to-existing-contact') }}
          </button>

          <!-- Search Input -->
          <div class="form-group" v-else>
            <input
              class="control"
              v-model="search_term.person"
              :placeholder="$t('mail.search-contact')"
              @keyup="search('person')"
            />

            <!-- Lookup Results -->
            <div class="lookup-results" v-if="search_term.person.length">
              <ul>
                <li v-for="(result, index) in search_results.person" :key="index" @click="linkLookup('person', result)">
                  <span>@{{ result.name }}</span>
                </li>
                <li v-if="!search_results.person.length && search_term.person.length && !is_searching.person">
                  <span>{{ $t('common.no-result-found') }}</span>
                </li>
              </ul>
            </div>

            <!-- Close Icon -->
            <i v-if="!is_searching.person" class="icon ri-close-line" @click="enabled_search.person = false; reset('person')"></i>

            <!-- Loader Icon -->
            <i v-if="is_searching.person" class="icon ri-loader-4-line"></i>
          </div>

          <!-- Add New Contact Button -->
          <button
            class="btn btn-sm btn-primary"
            @click="$root.openModal('addPersonModal')"
            >
            {{ $t('mail.create-new-contact') }}
          </button>
        </div>
      </div>

      <!-- Linked Person -->
      <div v-else>
        <div class="panel-header">
          {{ $t('mail.linked-contact') }}
          <span class="links">
            <Link :href="'/contacts/persons/edit/' + email.person_id" target="_blank">
              <i class="icon external-link-icon"></i>
            </Link>
            <i class="icon ri-close-line" @click="unlink('person')"></i>
          </span>
        </div>

        <div class="contact-details">
          <div class="name">@{{ email.person.name }}</div>
          <div class="email"><i class="icon emails-icon"></i>@{{ email.person.name }}</div>
        </div>
      </div>

      <!-- View Render Event after linking a person -->

      <!-- View Render Event before linking a lead -->
      <!-- Linking a Lead -->
      <div class="link-lead" v-if="email == undefined || !email.lead_id">
        <h3>{{ $t('mail.link-lead') }}</h3>

        <div class="btn-group">
          <button
            class="btn btn-sm btn-primary-outline"
            @click="enabled_search.lead = true"
            v-if="! enabled_search.lead"
            >
            {{ $t('mail.link-to-existing-lead') }}
          </button>

          <!-- Search Input -->
          <div class="form-group" v-else>
            <input
              class="control"
              v-model="search_term.lead"
              :placeholder="$t('mail.search-lead')"
              @keyup="search('lead')"
            />

            <!-- Lookup Results -->
            <div class="lookup-results" v-if="search_term.lead.length">
              <ul>
                <li v-for="(result, index) in search_results.lead" :key="index" @click="linkLookup('lead', result)">
                  <span>@{{ result.title }}</span>
                </li>
                <li v-if="!search_results.lead.length && search_term.lead.length && !is_searching.lead">
                  <span>{{ $t('common.no-result-found') }}</span>
                </li>
              </ul>
            </div>

            <!-- Close Icon -->
            <i v-if="!is_searching.lead" class="icon ri-close-line" @click="enabled_search.lead = false; reset('lead')"></i>

            <!-- Loader Icon -->
            <i v-if="is_searching.lead" class="icon ri-loader-4-line"></i>
          </div>

          <!-- Add New Lead Button -->
          <button
            class="btn btn-sm btn-primary"
            @click="$root.openModal('addLeadModal')"
            >
            {{ $t('mail.add-new-lead') }}
          </button>
        </div>
      </div>

      <!-- Linked Lead -->
      <div v-else>
        <div class="panel-header">
          {{ $t('mail.linked-lead') }}
          <span class="links">
            <Link :href="'/leads/view/' + email.lead_id" target="_blank">
              <i class="icon external-link-icon"></i>
            </Link>
            <i class="icon ri-close-line" @click="unlink('lead')"></i>
          </span>
        </div>

        <!-- <div class="panel-body">
          <div class="custom-attribute-view" v-html="html"></div>
        </div> -->
      </div>
      <!-- View Render Event after linking a lead -->
    </div>
  </BOffcanvas>
</template>

<script>
import { ref, toRefs, reactive, computed, watch, onMounted } from 'vue';
import { mapState, mapActions } from "vuex";
import { Link, usePage } from "@inertiajs/vue3";
import { successNotify, errorNotify } from "@/common/toast";

export default {
  components: {
    Link
  },
  computed: {
    ...mapState("data", {
      sidebarFilter: (state) => state.sidebarFilter,
    }),
  },
  methods: {
    ...mapActions("data", ["toggleSidebarFilter"]),
  },
  setup(props) {
    const show_filter = ref(false);

    const { props: pageProps } = usePage();

    let {
      email,
      html
    } = pageProps;

    const is_searching = reactive({
      person: false,
      lead: false,
    });

    const search_term = reactive({
      person: '',
      lead: '',
    });

    const search_results = reactive({
      person: [],
      lead: [],
    });

    const enabled_search = reactive({
      person: false,
      lead: false,
    });

    const search_routes = {
      person: "/contacts/persons/search",
      lead: "/leads/search",
    };

    const search = (type) => {
      is_searching[type] = true;
      if (search_term[type].length < 2) {
        search_results[type] = [];
        is_searching[type] = false;
        return;
      }

      axios.get(search_routes[type], { params: { query: search_term[type] } })
        .then(response => {
          search_results[type] = response.data;
          is_searching[type] = false;
          console.log(search_results, search_term);
        })
        .catch(error => {
          is_searching[type] = false;
        });
    };

    const linkLookup = (type, entity) => {
      const data = (type === 'person') ? { 'person_id': entity.id } : { 'lead_id': entity.id };
      const self = this;
      
      axios.post("/mail/edit/" + email.id, data)
        .then(response => {
          successNotify(response.data.message);  
          console.log(email); 
          email[type] = entity;
          if (type === 'lead') {
            html = response.data.html;
          }
          email[type + '_id'] = entity.id;
          reset(type);
        })
        .catch(error => {});
    };

    const unlink = (type) => {
      const data = (type === 'person') ? { 'person_id': null } : { 'lead_id': null };
      const self = this;

      axios.post("/mail/edit/" + email.id, data)
        .then(response => {
          successNotify(response.data.message);   
          email[type] = email[type + '_id'] = null;
        })
        .catch(error => {});
    };

    const reset = (type) => {
      search_term[type] = '';
      search_results[type] = [];
      is_searching[type] = false;
    };

    onMounted(() => {
      if (!Array.isArray(window.serverErrors)) {
          // $root.openModal('addPersonModal');
          // setTimeout(() => {
          //   $root.addServerErrors('person-form');
          // });
      }
    });

    return {
      show_filter,
      is_searching,
      search_term,
      search_results,
      enabled_search,
      search,
      linkLookup,
      unlink,
      reset,
      email,
    };
  },
};
</script>
<style scoped>
  .email-action-content {
    display: flex;
    flex-direction: column;
    padding: 20px;
  }
  .btn-group {
    display: grid;
  }
  .btn-group button {
    font-size: 18px;
    margin-bottom: 10px;
    width: 100%;
  }
  .btn.btn-primary-outline {
    background: #fff;
    border: 2px solid #0e90d9;
    color: #0e90d9;
  }
  .btn.btn-sm {
      font-size: 14px;
      height: 40px;
      padding: 7px 12px;
  }
  .btn {
      border: 2px solid transparent;
      border-radius: 4px;
      color: #fff;
      cursor: pointer;
      display: inline-block;
      font: inherit;
      font-weight: 500;
      padding: 10px 14px;
      transition: .2s cubic-bezier(.4,0,.2,1);
  }
  .btn.btn-primary {
    background: #0e90d9;
    color: #fff;
  }
  .form-group .control, .field-container .control {
    width: 100%;
    padding: 10px;
    margin-bottom: 5px;
    background: #FFFFFF;
    display: inline-block;
    vertical-align: middle;
    font-size: 18px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -ms-border-radius: 3px;
    border-radius: 3px;
    color: #546E7A;
    border: 1px solid #C1C2C3;
    transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    height: 40px;
}
.panel .btn-group .form-group {
    margin-bottom: 10px;
}
.form-group {
    color: #546e7a;
    display: block;
    font-size: 18px;
    margin-bottom: 10px;
    position: relative;
    width: 100%;
}
.form-group .icon {
    position: absolute;
    right: 13px;
    top: 17px;
}
.icon {
    background-size: cover;
    display: inline-block;
    height: 20px;
    vertical-align: top;
    width: 20px;
}
</style>
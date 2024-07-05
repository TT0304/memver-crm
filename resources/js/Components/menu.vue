<script>
import { Link, router } from "@inertiajs/vue3";
import { layoutComputed } from "@/state/helpers";
import simplebar from "simplebar-vue";

export default {
  components: {
    simplebar,
    Link,
  },
  data() {
    return {
      settings: {
        minScrollbarLength: 60,
      },
    };
  },
  computed: {
    ...layoutComputed,
    layoutType: {
      get() {
        return this.$store ? this.$store.state.layout.layoutType : {} || {};
      },
    },
  },
  mounted() {
    this.initActiveMenu();
    this.onRoutechange();
    if (document.querySelectorAll(".navbar-nav .collapse")) {
      let collapses = document.querySelectorAll(".navbar-nav .collapse");

      collapses.forEach((collapse) => {
        // Hide sibling collapses on `show.bs.collapse`
        collapse.addEventListener("show.bs.collapse", (e) => {
          e.stopPropagation();
          let closestCollapse = collapse.parentElement.closest(".collapse");
          if (closestCollapse) {
            let siblingCollapses = closestCollapse.querySelectorAll(".collapse");
            siblingCollapses.forEach((siblingCollapse) => {
              if (siblingCollapse.classList.contains("show")) {
                siblingCollapse.classList.remove("show");
                siblingCollapse.parentElement.firstChild.setAttribute(
                  "aria-expanded",
                  "false"
                );
              }
            });
          } else {
            let getSiblings = (elem) => {
              // Setup siblings array and get the first sibling
              let siblings = [];
              let sibling = elem.parentNode.firstChild;
              // Loop through each sibling and push to the array
              while (sibling) {
                if (sibling.nodeType === 1 && sibling !== elem) {
                  siblings.push(sibling);
                }
                sibling = sibling.nextSibling;
              }
              return siblings;
            };
            let siblings = getSiblings(collapse.parentElement);
            siblings.forEach((item) => {
              if (item.childNodes.length > 2) {
                item.firstElementChild.setAttribute("aria-expanded", "false");
                item.firstElementChild.classList.remove("active");
              }
              let ids = item.querySelectorAll("*[id]");
              ids.forEach((item1) => {
                item1.classList.remove("show");
                item1.parentElement.firstChild.setAttribute("aria-expanded", "false");
                item1.parentElement.firstChild.classList.remove("active");
                if (item1.childNodes.length > 2) {
                  let val = item1.querySelectorAll("ul li a");

                  val.forEach((subitem) => {
                    if (subitem.hasAttribute("aria-expanded"))
                      subitem.setAttribute("aria-expanded", "false");
                  });
                }
              });
            });
          }
        });

        // Hide nested collapses on `hide.bs.collapse`
        collapse.addEventListener("hide.bs.collapse", (e) => {
          e.stopPropagation();
          let childCollapses = collapse.querySelectorAll(".collapse");
          childCollapses.forEach((childCollapse) => {
            let childCollapseInstance = childCollapse;
            childCollapseInstance.classList.remove("show");
            childCollapseInstance.parentElement.firstChild.setAttribute(
              "aria-expanded",
              "false"
            );
          });
        });
      });
    }
  },

  methods: {
    onRoutechange() {
      // this.initActiveMenu();
      setTimeout(() => {
        var currentPath = window.location.pathname;
        if (document.querySelector("#navbar-nav")) {
          let currentPosition = document
            .querySelector("#navbar-nav")
            .querySelector('[href="' + currentPath + '"]')?.offsetTop;
          if (currentPosition > document.documentElement.clientHeight) {
            document.querySelector("#scrollbar .simplebar-content-wrapper")
              ? (document.querySelector(
                  "#scrollbar .simplebar-content-wrapper"
                ).scrollTop = currentPosition + 300)
              : "";
          }
        }
      }, 500);
    },
    getMenuByUrl() {
      var currentPath = window.location.pathname;
      let links = document.querySelectorAll("#navbar-nav a"); // Get all links within #navbar-nav
      let menuLink = null;
      links.forEach((link) => {
        if (currentPath.startsWith(link.getAttribute("href"))) menuLink = link;
      });
      return menuLink;
    },
    initActiveMenu() {
      setTimeout(() => {
        if (document.querySelector("#navbar-nav")) {
          let a = this.getMenuByUrl();
          if (a) {
            a.classList.add("active");
            let parentCollapseDiv = a.closest(".collapse.menu-dropdown");
            if (parentCollapseDiv) {
              parentCollapseDiv.classList.add("show");
              parentCollapseDiv.parentElement.children[0].classList.add("active");
              parentCollapseDiv.parentElement.children[0].setAttribute(
                "aria-expanded",
                "true"
              );
              if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
                parentCollapseDiv.parentElement
                  .closest(".collapse")
                  .classList.add("show");
                if (
                  parentCollapseDiv.parentElement.closest(".collapse")
                    .previousElementSibling
                )
                  parentCollapseDiv.parentElement
                    .closest(".collapse")
                    .previousElementSibling.classList.add("active");
                const grandparent = parentCollapseDiv.parentElement
                  .closest(".collapse")
                  .previousElementSibling.parentElement.closest(".collapse");
                if (grandparent && grandparent && grandparent.previousElementSibling) {
                  grandparent.previousElementSibling.classList.add("active");
                  grandparent.classList.add("show");
                }
              }
            }
          }
        }
      }, 0);
    },
  },
};
</script>

<template>
  <BContainer fluid>
    <div id="two-column-menu"></div>

    <template v-if="layoutType === 'vertical' || layoutType === 'semibox'">
      <ul class="navbar-nav h-100" id="navbar-nav">
        <li class="menu-title">
          <span data-key="t-menu"> {{ $t("t-menu") }}</span>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-link" href="/" role="button">
            <i class="ri-dashboard-2-line"></i>
            <span data-key="t-dashboards"> {{ $t("t-dashboards") }}</span>
          </a>
        </li>
        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('lead_access')" class="nav-item">
          <a class="nav-link menu-link" href="/leads" role="button">
            <i class="ri-filter-line"></i>
            <span data-key="t-leads"> {{ $t("t-leads") }}</span>
          </a>
        </li>
        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('quote_access')" class="nav-item">
          <a class="nav-link menu-link" href="/quotes" role="button">
            <i class="ri-file-text-line"></i>
            <span data-key="dashboard.quotes"> {{ $t("dashboard.quotes") }}</span>
          </a>
        </li>

        
        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_access')" class="nav-item">
          <a
            class="nav-link menu-link"
            href="#sidebarMail"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarMail"
          >
            <i class="ri-mail-line"></i>
            <span data-key="layouts.mail.title">{{ $t("layouts.mail.title") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="sidebarMail">
            <ul class="nav nav-sm flex-column">
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_compose_access')" class="nav-item">
                <a href="/mail/compose" class="nav-link" data-key="mail.compose">
                  {{ $t("mail.compose") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_inbox_access')" class="nav-item">
                <a href="/mail/inbox" class="nav-link" data-key="mail.inbox">
                  {{ $t("mail.inbox") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_draft_access')" class="nav-item">
                <a href="/mail/draft" class="nav-link" data-key="mail.draft">
                  {{ $t("mail.draft") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_outbox_access')" class="nav-item">
                <a href="/mail/outbox" class="nav-link" data-key="mail.outbox">
                  {{ $t("mail.outbox") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_sent_access')" class="nav-item">
                <a href="/mail/sent" class="nav-link" data-key="mail.sent">
                  {{ $t("mail.sent") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('mail_trash_access')" class="nav-item">
                <a href="/mail/trash" class="nav-link" data-key="mail.trash">
                  {{ $t("mail.trash") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('email_template_access')" class="nav-item">
                <a href="/email_templates" class="nav-link" data-key="mail.template">
                  {{ $t("mail.template") }}
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('product_access')" class="nav-item">
          <a class="nav-link menu-link" href="/products" role="button">
            <i class="bx bx-box"></i>
            <span data-key="dashboard.products"> {{ $t("dashboard.products") }}</span>
          </a>
        </li>

        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('contacts_access')" class="nav-item">
          <a
            class="nav-link menu-link"
            href="#sidebarContact"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarContact"
          >
            <i class="ri-pages-line"></i>
            <span data-key="t-contacts">{{ $t("t-contacts") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="sidebarContact">
            <ul class="nav nav-sm flex-column">
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('person_access')" class="nav-item">
                <a href="/contacts/persons" class="nav-link" data-key="contacts.persons.title">
                  {{ $t("contacts.persons.title") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('organization_access')" class="nav-item">
                <a href="/contacts/organizations" class="nav-link" data-key="contacts.organizations.title">
                  {{ $t("contacts.organizations.title") }}
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('settings_access')" class="nav-item">
          <a
            class="nav-link menu-link"
            href="#sidebarSettings"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="sidebarSettings"
          >
            <i class="ri-pages-line"></i>
            <span data-key="t-settings">{{ $t("t-settings") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="sidebarSettings">
            <ul class="nav nav-sm flex-column">
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('permission_access')" class="nav-item">
                <a href="/settings/permissions" class="nav-link" data-key="settings.users.permission">
                  {{ $t("settings.users.permission") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('role_access')" class="nav-item">
                <a href="/settings/roles" class="nav-link" data-key="settings.users.role">
                  {{ $t("settings.users.role") }}
                </a>
              </li>
              <li v-if="$page.props.auth.user.permissions?.map((pr)=>pr.name).includes('user_access')" class="nav-item">
                <a href="/settings/users" class="nav-link" data-key="settings.users.title">
                  {{ $t("settings.users.title") }}
                </a>
              </li>
            </ul>
          </div>
        </li>
        
        <!-- end Dashboard Menu -->
      </ul>
    </template>
  </BContainer>
</template>

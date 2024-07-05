<template>
  <div class="modal-container" v-if="isModalOpen" style="z-index: 10000">
      <div class="modal-header">
          <slot name="header">
              <slot name="header-title">
                  <h3>Default header</h3>
              </slot>

              <div class="header-actions">
                  <slot name="header-actions">
                      <i class="icon ri-close-line" @click="closeModal"></i>
                  </slot>
              </div>
          </slot>
      </div>

      <div class="modal-body">
          <slot name="body">
              Default body
          </slot>
      </div>
  </div>
</template>

<script>
  export default {
      props: ['id', 'isOpen'],

      mounted () {
          this.closeModal();
      },
      computed: {
          isModalOpen () {
              this.addClassToBody();
              return this.isOpen;
          }
      },
      methods: {
          closeModal () {
              this.$root.modalIds[this.id] = false;
          },

          addClassToBody () {
              const body = document.querySelector("body");

              if (this.isOpen) {
                  body.classList.add("modal-open");
              } else {
                  body.classList.remove("modal-open");
              }
          }
      }
  }
</script>

<style scoped>
  .icon {
      background-size: cover;
      display: inline-block;
      height: 20px;
      vertical-align: top;
      width: 20px;
  }
</style>

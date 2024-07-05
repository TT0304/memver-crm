<template>
    <div class="alert" :class="flash.type">
      <span class="icon" :class="icons[flash.type]"></span>
      <p>{{ flash.message }}</p>
      <span class="icon close-white-icon" @click="remove"></span>
    </div>
  </template>
  
  <script>
  export default {
    props: ['flash'],
  
    data() {
      return {
        icons: {
          success: 'circle-check-white-icon',
          warning: 'circle-info-white-icon',
          error: 'circle-close-white-icon',
          info: 'circle-info-white-icon',
        },
      };
    },
  
    mounted() {
      // setTimeout(() => {
      //   this.$emit('onRemoveFlash', this.flash);
      // }, 5000);
    },
  
    methods: {
      remove() {
        this.$el.remove();
      },
    },
  };
  </script>
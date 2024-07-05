<template>
    <transition-group
      tag="div"
      name="flash-wrapper"
      class="alert-wrapper"
    >
      <Flash
        v-for="(flash) in flashes"
        :key="flash.uid"
        :flash="flash"
        @onRemoveFlash="removeFlash($event)"
      ></Flash>
    </transition-group>
  </template>
  
  <script>
  import { ref } from 'vue';
  import Flash from '@/Components/flash.vue'; // Assuming Flash component is defined in a separate file
  
  export default {
    components: {
      Flash,
    },
    setup() {
      const uid = ref(1);
      const flashes = ref([]);
  
      const addFlash = (flash) => {
        flash.uid = uid.value++;
        flashes.value.push(flash);
      };
  
      const removeFlash = (flash) => {
        const index = flashes.value.indexOf(flash);
        if (index !== -1) {
          flashes.value.splice(index, 1);
        }
      };
  
      return {
        flashes,
        addFlash,
        removeFlash,
      };
    },
  };
  </script>
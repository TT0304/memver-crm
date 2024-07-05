<template>
  <BTab :title="name" :active="isActive" ref="tabRef">
    <slot></slot>
  </BTab>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import eventBus from "@/common/eventBus";
const hasError = ref(false);
const tabRef = ref(null);
const addHasErrorClass = () => {
  hasError.value = true;
  const childrenWithErrors = tabRef.value.querySelectorAll('.is-invalid');
  if(childrenWithErrors > 0) {
    hasError.value = false;
  }
}
onMounted(() => {
  addHasErrorClass();
  eventBus.on("onFormError", this.addHasErrorClass);
});
</script>
<script>
export default {
  props: {
    name: {
      required: true,
    },
    selected: {
      default: false,
    },
  },

  data() {
    return {
      isActive: false,
      hasError: false,
    };
  },

  mounted() {

    this.isActive = this.selected;
  },

};
</script>

<template>
  <div v-if="Array.isArray(value)">
    <span class="multi-value" v-for="(item, index) in value" :key="index">
      {{ item.value }}
      <span>{{ " (" + item.label + ")" }}</span>
    </span>
  </div>
  <div v-else>
    {{ $t("common.not-available") }}
  </div>
</template>

<script>
export default {
  props: ["value", "attribute"],
};
</script>

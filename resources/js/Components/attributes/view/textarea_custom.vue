<template>
  {{ value ?? $t('common.not-available') }}
</template>

<script>
export default {
  props: ["value", "attribute"],
};
</script>

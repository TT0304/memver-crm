<template>
  {{ entity ? entity.name : $t('common.not-available') }}
</template>

<script>
import axios from "axios";
export default {
  props: ["value", "attribute"],
  data() {
    return {
      entity: null
    }
  },
  mounted () {
    const self = this;
    if(this.attribute?.lookup_type) {
      axios.get(`${route("settings.attributes.lookup_entity")}/${this.attribute.lookup_type}`, 
      { params: { value: self.value } }).then((res) => {
        this.entity = res;
      }).catch((err) => {
        this.entity = null;
      })
    }
  }
};
</script>

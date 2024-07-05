<template>
  {{ !this.pageLoaded ? '' : entity ? entity.name : $t('common.not-available') }}
</template>

<script>
import axios from "axios";
export default {
  props: ["value", "attribute"],
  data() {
    return {
      entity: null,
      pageLoaded: false,
    }
  },
  mounted () {
    const self = this;
    if(this.attribute?.lookup_type) {
      axios.get(`${route("settings.attributes.lookup_entity")}/${this.attribute.lookup_type}`, 
      { params: { value: self.value } }).then((res) => {
        this.entity = res.data;
        this.pageLoaded = true;
      }).catch((err) => {
        this.entity = null;
        this.pageLoaded = true;
      })
    }
  }
};
</script>

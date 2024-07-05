<template>
  <flat-pickr class="datetime-field" :config="timeConfig" />
</template>
<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
  components: {
    flatPickr,
  },
  data() {
    const now = new Date();

    return {
      timeConfig: {
        utc: false,
        enableTime: false,
        defaultDate: now,
      },
    };
  },

  mounted: function () {},
};
</script>
<style>
.datetime-field {
  border: 2px solid #c7c7c7;
  border-radius: 3px;
  font-family: "Montserrat", sans-serif;
  height: 35px;
  padding-left: 5px;
  color: gray;
}
</style>

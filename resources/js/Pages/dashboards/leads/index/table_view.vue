<template>
  <TableComponent :dataSrc="route('dashboards.leads.get')">
    <template v-slot:extra-filters>
      <ViewSwitch></ViewSwitch>
    </template>
  </TableComponent>
</template>

<script>
import ViewSwitch from "@/Pages/dashboards/leads/index/view_switch.vue";
import TableComponent from "@/Components/datagrid/table.vue";
export default {
  props: [],
  components: {
    ViewSwitch,
    TableComponent,
  },
  computed: {},
  mounted() {},
  data() {
    return {};
  },
  methods: {},
};
</script>

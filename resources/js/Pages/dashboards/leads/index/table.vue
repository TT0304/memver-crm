<template>
  <BCard no-body>
    <BCardBody>
      <BRow class="g-2">
        <TableComponent>
          <template v-slot:extra-filter>
            <ViewSwitch></ViewSwitch>
          </template>
          
        </TableComponent>
        
      </BRow>
    </BCardBody>
  </BCard>
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

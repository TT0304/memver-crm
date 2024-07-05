<template>
  <div
    class="d-flex align-items-top gap-2"
    :class="
      errors[attribute['code'] + '.address'] ||
      errors[attribute['code'] + '.country'] ||
      errors[attribute['code'] + '.state'] ||
      errors[attribute['code'] + '.city'] ||
      errors[attribute['code'] + '.postcode']
        ? 'is-invalid'
        : ''
    "
  >
    <div class="flex-grow-1">
      <VField
        :name="attribute.code + '.address'"
        v-model="address"
        :rules="rules"
        :label="$t('common.address')"
      >
        <BFormTextarea
          :name="attribute.code + '.address'"
          v-model="address"
          :state="errors[attribute.code + '.address']"
          class="form-control h-100"
          :class="errors[attribute['code'] + '.address'] ? 'is-invalid' : ''"
        ></BFormTextarea>
      </VField>
    </div>
    <div class="flex-grow-1">
      <VField
        :name="attribute.code + '.country'"
        v-model="country"
        :rules="rules"
        :label="$t('common.country')"
      >
        <BFormSelect
          :name="attribute.code + '.country'"
          v-model="country"
          class="mb-2"
          :class="errors[attribute['code'] + '.country'] ? 'is-invalid' : ''"
        >
          <BFormSelectOption :value="null">{{
            $t("common.select-country")
          }}</BFormSelectOption>
          <BFormSelectOption
            v-for="(country, index) in countries"
            :key="index"
            :value="country.code"
            >{{ country.name }}</BFormSelectOption
          >
        </BFormSelect>
      </VField>
      <VField
        :name="attribute.code + '.state'"
        v-model="state"
        :rules="rules"
        :label="$t('common.state')"
        v-if="haveStates()"
      >
        <BFormSelect
          :name="attribute.code + '.state'"
          v-model="state"
          class="mb-2"
          :class="errors[attribute['code'] + '.state'] ? 'is-invalid' : ''"
        >
          <BFormSelectOption :value="null">{{
            $t("common.select-state")
          }}</BFormSelectOption>
          <BFormSelectOption
            v-for="(state, index) in groupStatesByCountries[country]"
            :key="index"
            :value="state.code"
            >{{ state.name }}</BFormSelectOption
          >
        </BFormSelect>
      </VField>
      <VField
        :name="attribute.code + '.state'"
        v-model="state"
        :rules="rules"
        :label="$t('common.state')"
        v-else
      >
        <BFormInput
          :name="attribute.code + '.state'"
          v-model="state"
          class="mb-2"
          :class="errors[attribute['code'] + '.state'] ? 'is-invalid' : ''"
          :placeholder="$t('common.state')"
        >
        </BFormInput>
      </VField>

      <VField
        :name="attribute.code + '.city'"
        :rules="rules"
        :value="data && data['city'] ? data['city'] : ''"
        :label="$t('common.city')"
      >
        <BFormInput
          :name="attribute.code + '.city'"
          v-model="city"
          class="mb-2"
          :class="errors[attribute['code'] + '.city'] ? 'is-invalid' : ''"
          :placeholder="$t('common.city')"
        >
        </BFormInput>
      </VField>

      <VField
        :name="attribute.code + '.postcode'"
        :rules="rules"
        :value="data && data['postcode'] ? data['postcode'] : ''"
        :label="$t('common.postcode')"
      >
        <BFormInput
          :name="attribute.code + '.postcode'"
          v-model="postcode"
          class="mb-2"
          :class="errors[attribute['code'] + '.postcode'] ? 'is-invalid' : ''"
          :placeholder="$t('common.postcode')"
        >
        </BFormInput>
      </VField>
    </div>
  </div>
  <BFormInvalidFeedback
    v-if="
      errors[attribute['code'] + '.address'] ||
      errors[attribute['code'] + '.country'] ||
      errors[attribute['code'] + '.state'] ||
      errors[attribute['code'] + '.city'] ||
      errors[attribute['code'] + '.postcode']
    "
    >{{ $t("common.address-validation") }}
  </BFormInvalidFeedback>
</template>

<script>
import { mapState } from "vuex";
export default {
  props: ["attribute", "rules", "errors", "data", "cc", "bcc"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  data() {
    return {
      country: this.data ? this.data["country"] : null,
      state: this.data ? this.data["state"] : null,
      city: this.data ? this.data["city"] : null,
      postcode: this.data ? this.data["postcode"] : null,
      address: this.data ? this.data["address"] : null,
    };
  },
  methods: {
    haveStates: function () {
      if (
        this.country &&
        this.groupStatesByCountries &&
        this.groupStatesByCountries[this.country] &&
        this.groupStatesByCountries[this.country].length
      ) {
        return true;
      }
      return false;
    },
  },
  computed: {
    ...mapState("data", {
      groupStatesByCountries: (state) => state.groupStatesByCountries,
      countries: (state) => state.countries,
    }),
  },
};
</script>

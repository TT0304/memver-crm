<template>
  <div class="attachment-item" v-show="show">
    <span>
      <input
        type="file"
        :name="inputName"
        @change="attachmentSelected"
        ref="attachmentRef"
      />

      {{ name }}

      <i class="mdi mdi-window-close" @click="removeAttachment"></i>
    </span>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  props: {
    attachment: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    inputName: {
      type: String,
      required: false,
    },
  },

  setup(props, { emit }) {
    const show = ref(false);
    const name = ref("");
    const attachmentRef = ref(null); // Define a ref

    const activate = () => {
      if (props.attachment && props.attachment.isNew) {
        openFileBrowser();
      } else {
        showExistingAttachment();
      }
    };

    const openFileBrowser = () => {
      attachmentRef.value.click(); // Access the DOM element directly
    };

    const attachmentSelected = (event) => {
      let attachment = event.target;

      if (attachment.files && attachment.files[0]) {
        show.value = true;
        name.value = attachment.files[0].name;
      }
    };

    const showExistingAttachment = () => {
      show.value = true;
      name.value = props.attachment.name;
    };

    const removeAttachment = () => {
      emit("onRemoveAttachment", props.attachment);
    };

    onMounted(() => {
      activate();
    });

    return {
      show,
      name,
      attachmentRef,
      openFileBrowser,
      attachmentSelected,
      showExistingAttachment,
      removeAttachment,
    };
  },
};
</script>

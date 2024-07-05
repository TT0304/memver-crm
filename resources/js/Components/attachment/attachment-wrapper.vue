<template>
    <div class="attachment-wrapper">
      <div
        @dragcenter.prevent="toggleActive"
        @dragleave.prevent="toggleActive"
        @dragover.prevent
        @drop.prevent="drop"
        :class="{ 'active-dropzone': active }"
        class="dropzone"
      >
        <span>{{$t("edits.dragdropfile")}}</span>
  
        <span>{{$t("edits.or")}}</span>
  
        <attachment-item
          v-for="attachment in attachments"
          :key="attachment.id"
          :attachment="attachment"
          input-name="attachments[]"
          @onRemoveAttachment="removeAttachment"
        ></attachment-item>
  
        <label class="add-attachment-link" @click="addAttachment">
          <i class="ri-attachment-line"></i>
          {{$t("edits.add_ttachment")}}
        </label>
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import AttachmentItem from "@/Components/attachment/attachment-item.vue";
  
  export default {
    props: ["rules", "attribute", "data", "cc", "bcc", "errors"],
    emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
    setup(props) {
      const active = ref(false);
      const attachmentCount = ref(0);
      const attachments = ref([]);

      props.data&&props.data.forEach(function (attachment) {
          attachment.isNew = false;

          attachments.value.push(attachment);

          attachmentCount.value++;
      });
  
      const addAttachment = () => {
        attachmentCount.value++;
        attachments.value.push({
          id: 'attachment_' + attachmentCount.value,
          isNew: true,
        });
      };
  
      const drop = (event) => {
        const files = event.dataTransfer.files;
        for (let i = 0; i < files.length; i++) {
            attachmentCount.value++;
            const id = 'attachment_' + attachmentCount.value + '_' + i;
            attachments.value.push({
            id,
            isNew: false,
            type: 'dropzone',
            file: files[i],
            name: files[i].name, // Include the file name in the attachment object
            });
        }
        };
        
      const toggleActive = () => {
        active.value = !active.value;
      };
  
      const removeAttachment = (attachment) => {
        const index = attachments.value.indexOf(attachment);
        attachments.value.splice(index, 1);
      };
  
      return {
        active,
        attachmentCount,
        attachments,
        addAttachment,
        drop,
        toggleActive,
        removeAttachment,
      };
    },
    components: {
      AttachmentItem,
    },
  };
  </script>
  
  <style>
  .active-dropzone {
    border-style: dashed;
    border-color: darkgrey;
    color: grey;
  }
  
  .active-dropzone input {
    display: none;
  }
  
  .attachment-wrapper .attachment-item span {
    word-break: break-all;
  }
  </style>
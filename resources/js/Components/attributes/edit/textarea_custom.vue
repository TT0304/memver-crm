<template>
  <VField
    :name="attribute.code"
    :rules="rules"
    :label="attribute.name"
    v-model="textValue"
    v-slot="{ errors }"
  >
    <ckeditor v-if="attribute.code == 'reply'" v-model="textValue" :editor="editor" :config="editorConfig" class="form-control"
      :class="{ 'is-invalid': errors.length }"></ckeditor>
    <froala
      v-if="attribute.code !== 'reply'"
      :id="attribute.code"
      :tag="'textarea'"
      :config="config"
      v-model:value="textValue"
      class="form-control"
      :class="{ 'is-invalid': errors.length }"
    />
  </VField>
  <div class="invalid-feedback">{{ errors[attribute.code] }}</div>
</template>

<script>
import i18n from "../../../i18n";
import 'froala-editor/js/froala_editor.pkgd.min.js';
import 'froala-editor/css/froala_editor.pkgd.min.css';
import 'froala-editor/css/froala_style.min.css';

import 'froala-editor/js/plugins.pkgd.min.js';

import 'froala-editor/js/third_party/embedly.min';
import 'froala-editor/js/third_party/font_awesome.min';
import 'froala-editor/js/third_party/spell_checker.min';
import 'froala-editor/js/third_party/image_tui.min';
import FroalaEditor from 'froala-editor';

import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
  props: ["attribute", "rules", "errors", "data", "cc", "bcc", "placeholdersData"],
  emits: ['toggle-cc', 'toggle-bcc'], // Declare custom event listeners
  data() {
    return {
      editor: ClassicEditor,
      textValue: (this.data ? this.data : ''),
      lan: i18n.locale,
      config: {
        toolbarButtons: {
          'moreText': {
            'buttons': [ 'Placeholders', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']
          },
          'moreParagraph': {
            'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple']
          },
          'moreRich': {
            'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']
          },
          'moreMisc': {
            'buttons': ['undo', 'redo', 'fullscreen', 'spellChecker', 'selectAll', 'html', 'help'],
            'align': 'right',
            'buttonsVisible': 2
          }
        },
        pluginsEnabled: this.attribute.code == "reply" ? ['align', 'codeView', 'colors', 'emoticons', 'entities', 'fontFamily', 'fontSize', 'fullscreen', 'image', 'imageManager', 'inlineStyle', 'link', 'lists', 'paragraphFormat', 'paragraphStyle', 'quote', 'save', 'table', 'url', 'video', '|', 'table'] : ['Placeholders', 'align', 'codeView', 'colors', 'emoticons', 'entities', 'fontFamily', 'fontSize', 'fullscreen', 'image', 'imageManager', 'inlineStyle', 'link', 'lists', 'paragraphFormat', 'paragraphStyle', 'quote', 'save', 'table', 'url', 'video', '|', 'table'],
        height: 200,
        menubar: this.attribute.code === "reply",
        language: i18n.global.locale,
      },
      editorConfig: {}
    }
  },
  created() {
    const vueInstance = this; 
    const options = {};
    if(vueInstance.attribute.code == "reply"){
      return;
    }
    vueInstance.placeholdersData.forEach(entity => {
      entity.menu.forEach(option => {
        options[option.value] = `${entity.text}.${option.text}`;
      });
    });
    
    FroalaEditor.DefineIcon('Placeholders');
    FroalaEditor.RegisterCommand('Placeholders', {
      title: 'Placeholders',
      type: 'dropdown',
      focus: true,
      options: options,
      undo: true,
      refreshAfterCallback: true,
      callback: function (cmd, val, params) {
        const editor = this;
        editor.html.insert(val);
        vueInstance.hideUnlicensedMessage()
      },
      refresh: function () {
        vueInstance.hideUnlicensedMessage();
      },
      refreshOnShow: function () {
        vueInstance.hideUnlicensedMessage();
      }
    });
  },
  components: {
    ckeditor: CKEditor.component,
  },
  mounted() {
    this.hideUnlicensedMessage();
  },
  methods: {
    hideUnlicensedMessage() {
      const interval = setInterval(() => {
        const licenseMessage = document.querySelector('a[href*="https://www.froala.com/wysiwyg-editor?k=u"]');
        if (licenseMessage && licenseMessage.innerText.includes("Unlicensed copy of the Froala Editor. Use it legally by purchasing a license.")) {
          licenseMessage.style.display = 'none';
          clearInterval(interval);
        }
      }, 100);
    }
  },
  computed: {
    editorLanguage() {
      return i18n.global.locale === 'en' ? 'en' : 'ar';
    }
  },
  watch: {
    editorLanguage() {
      this.$nextTick(() => {
        window.location.reload();
        this.editorConfig.language = this.editorLanguage;
      });
    },
  }
};
</script>
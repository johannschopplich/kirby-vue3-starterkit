<template>
  <k-field
    :disabled="disabled"
    :label="label"
    :name="name"
    :required="required"
    :type="type"
  >

    <template v-if="inline">
      <k-editor-inline
        :autofocus="autofocus"
        :disabled="disabled"
        :placeholder="placeholder"
        :spellcheck="spellcheck"
        :value="value"
        @input="onInput"
      />
    </template>
    <template v-else>

      <k-dropdown slot="options">
        <k-button icon="dots" @click="$refs.settings.toggle()" />
        <k-dropdown-content ref="settings" align="right">
          <k-dropdown-item icon="upload" @click="upload">Import content</k-dropdown-item>
          <k-dropdown-item :disabled="isEmpty" icon="attachment" @click="$refs.download.open()">Export</k-dropdown-item>
          <hr>
          <k-dropdown-item :disabled="isEmpty" icon="trash" @click="$refs.removeAll.open()">Delete blocks</k-dropdown-item>
        </k-dropdown-content>
      </k-dropdown>

      <k-editor
        ref="editor"
        :autofocus="autofocus"
        :allowed="allowed"
        :disabled="disabled"
        :disallowed="disallowed"
        :endpoints="endpoints"
        :spellcheck="spellcheck"
        :value="value"
        @input="onInput"
      />

      <k-dialog
        ref="download"
        :button="$t('download')"
        @submit="onDownload"
      >
        <k-form :fields="$options.downloadFields" v-model="downloadOptions" />
      </k-dialog>

      <k-dialog
        ref="removeAll"
        :button="$t('delete')"
        theme="negative"
        @submit="onRemoveAll"
      >
        <k-text>Do you really want to remove all blocks?</k-text>
      </k-dialog>

      <k-upload ref="upload" @success="onUpload" />

    </template>

  </k-field>
</template>

<script>
import Clipboard from "clipboard";
import Editor from "./Editor.vue";
import EditorInline from "./EditorInline.vue";

export default {
  inheritAttrs: false,
  components: {
    "k-editor": Editor,
    "k-editor-inline": EditorInline,
  },
  downloadFields: {
    type: {
      label: "Download …",
      type: "select",
      required: true,
      empty: false,
      options: [
        { value: "md", text: "Markdown" },
        { value: "html", text: "HTML" },
        { value: "json", text: "JSON" },
      ]
    }
  },
  props: {
    allowed: [Array, Object],
    autofocus: Boolean,
    disabled: Boolean,
    disallowed: [Array, Object],
    endpoints: Object,
    inline: Boolean,
    label: String,
    name: String,
    placeholder: String,
    required: Boolean,
    spellcheck: Boolean,
    type: String,
    value: {
      type: [Array, Object],
      default() {
        return [];
      }
    }
  },
  data() {
    return {
      downloadOptions: {
        type: "md"
      }
    };
  },
  computed: {
    isEmpty() {
      return !Array.isArray(this.value) || this.value.length === 0;
    }
  },
  methods: {
    onDownload() {

      this.$api
        .post(this.endpoints.field + "/export", {
          data: this.value,
          type: this.downloadOptions.type
        })
        .then(response => {

          let a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";

          let blob = new Blob([response.data], { type: "octet/stream" });
          let url  = window.URL.createObjectURL(blob);

          a.href     = url;
          a.download = this.name + "." + this.downloadOptions.type;
          a.click();

          window.URL.revokeObjectURL(url);

          document.body.removeChild(a);

          this.$refs.download.close();
          this.$store.dispatch("notification/success", "The file has been downloaded");

        })
        .catch(error => {
          this.$refs.download.error(error.message);
        });
    },
    onInput(blocks) {
      this.$emit("input", blocks);
    },
    onRemoveAll() {
      this.$refs.editor.removeAll();
      this.$refs.removeAll.close();
    },
    onUpload(files, [blocks]) {
      this.$refs.editor.importBlocks(blocks);
    },
    upload() {
      this.$refs.upload.open({
        url: window.panel.api + "/" + this.endpoints.field + "/import",
        accept: "text/plain,text/html,text/markdown,application/json",
        multiple: false
      });
    }
  }
};
</script>

<style lang="scss">
/** put your css here **/
</style>

<template>
  <k-input
    :disabled="disabled"
    class="k-editor-inline"
    theme="field"
  >
    <k-editable
      ref="editor"
      :key="modified"
      :autofocus="autofocus"
      :breaks="true"
      :content="content"
      :disabled="disabled"
      :placeholder="placeholder"
      :spellcheck="spellcheck"
      @enter="onEnter"
      @split="onEnter"
      @input="onInput"
    />
  </k-input>
</template>

<script>
export default {
  props: {
    autofocus: Boolean,
    disabled: Boolean,
    placeholder: String,
    spellcheck: Boolean,
    value: String
  },
  data() {
    return {
      content: this.value,
      modified: new Date(),
    };
  },
  watch: {
    value(value) {
      if (value !== this.content) {
        this.content  = value;
        this.modified = new Date();
      }
    }
  },
  methods: {
    onEnter() {
      this.$refs.editor.insertBreak();
    },
    onInput(value) {
      if (value !== this.content) {
        this.content = value;
        this.$emit("input", value);
      }
    }
  }
};
</script>

<style lang="scss">
.k-editor-inline .k-editable,
.k-editor-inline .k-editable-placeholder {
  padding: .375rem;
}
</style>

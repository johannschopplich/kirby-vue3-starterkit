<script>
export default {
  icon: "quote",
  extends: "paragraph",
  breaks: true,
};
</script>

<style lang="scss">

.k-editor-blockquote-block {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.k-editor-blockquote-block .k-editable-placeholder,
.k-editor-blockquote-block .ProseMirror {
  font-size: 1.25rem;
  line-height: 1.5em;
  padding: 0 0 0 1rem;
  border-left: 3px solid #000;
}
.k-editor-blockquote-block .k-editor-block-options {
  top: 24px;
}
</style>

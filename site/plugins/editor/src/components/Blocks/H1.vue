<script>
import Paragraph from "./Paragraph.vue";

export default {
  extends: Paragraph,
  icon: "title",
  breaks: true,
  marks: [
    'italic',
    'strikeThrough',
    'underline',
    'code',
    'link'
  ]
};
</script>

<style lang="scss">
.k-editor-h1-block .k-editable-placeholder,
.k-editor-h1-block .k-editable .ProseMirror {
  font-size: 1.75rem;
  font-weight: 600;
  line-height: 1.25em;
}
.k-editor-h1-block .k-editor-block-options {
  top: 10px;
}
</style>

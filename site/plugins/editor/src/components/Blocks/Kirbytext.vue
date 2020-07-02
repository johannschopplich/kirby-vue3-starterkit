<script>
import ProseMirror from "../ProseMirror/ProseMirror.vue";

export default {
  extends: ProseMirror,
  icon: "markdown",
  breaks: true,
  code: true,
  props: {
    content: String,
    attrs: [Object, Array]
  },
  methods: {
    onInput(html) {
      this.$emit("input", {
        content: html
      });
    }
  }
};
</script>

<style lang="scss">
@import "variables.scss";

.k-editor-kirbytext-block {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.k-editor-kirbytext-block .k-editable-code pre {
  background: $color-background-transparent;
  padding: 1.5rem;
  border-radius: $border-radius;
  white-space: pre-wrap;
}
.k-editor-kirbytext-block .k-editor-block-options {
  top: 20px;
}
</style>

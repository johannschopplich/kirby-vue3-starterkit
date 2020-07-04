<script>
export default {
  extends: "paragraph",
  append: "ul",
  icon: "list-bullet",
  breaks: true,
  methods: {
    indent(indent) {
      this.$emit("update", {
        attrs: {
          indent: indent
        }
      });
    },
    onEnter() {
      if (this.isEmpty()) {
        this.$emit("convert", "paragraph");
      } else {
        this.$emit("append", {
          type: this.$options.append,
          attrs: {
            indent: this.attrs.indent || 0
          }
        });
      }
    },
  }
};
</script>

<style lang="scss">
.k-editor-ul-block .k-editable {
  position: relative;
  margin-left: 1.25rem;
  line-height: 1.5em;
}
.k-editor-ul-block .k-editable:before {
  position: absolute;
  content: "";
  top: .625em;
  left: -1.25rem;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}
</style>

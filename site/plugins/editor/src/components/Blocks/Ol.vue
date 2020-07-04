<script>
export default {
  extends: "ul",
  append: "ol",
  icon: "list-numbers",
};
</script>

<style lang="scss">
@import "variables.scss";

.k-editor-ol-block {
  counter-increment: list;
}
.k-editor-block:not(.k-editor-ol-block) {
  counter-reset: list;
}
.k-editor-ol-block .k-editable {
  position: relative;
  margin-left: 1.25rem;
  line-height: 1.5em;
}
.k-editor-ol-block .k-editable:before {
  position: absolute;
  content: counter(list) ".";
  top: .0625rem;
  left: -1.25rem;
  color: $color-text-lighter;
  font-size: $font-size-small;
}
</style>

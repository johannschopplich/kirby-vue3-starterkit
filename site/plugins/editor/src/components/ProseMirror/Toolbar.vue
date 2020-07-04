<template>
  <div class="k-editable-toolbar">
    <k-button
      v-for="(mark, markType) in options"
      :key="markType"
      :class="{'k-editable-toolbar-button': true, 'k-editable-toolbar-button-active': marks.includes(markType)}"
      :icon="mark.spec.toolbar.icon"
      @mousedown.prevent="$emit('option', mark.spec.toolbar)"
    />
  </div>
</template>

<script>
export default {
  props: {
    marks: {
      type: Array,
      default() {
        return []
      }
    },
    options: Object
  },
};
</script>

<style lang="scss">
@import "variables.scss";

.k-editable-toolbar {
  position: absolute;
  display: flex;
  background: $color-black;
  height: 36px;
  transform: translateX(-50%) translateY(-.75rem);
  z-index: 1;
  box-shadow: $box-shadow;
  color: $color-white;
  border-radius: $border-radius;
}
.k-editable-toolbar-button {
  display: flex;
  align-items: center;
  height: 36px;
  padding: 0 .5rem;
  font-size: $font-size-small !important;
  color: currentColor;
}
.k-editable-toolbar-button.k-editable-toolbar-button-active {
  color: $color-focus-light;
}
</style>

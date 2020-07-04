<template>
  <nav class="k-editor-block-options">
    <button :disabled="!sortable" type="button" class="k-editor-block-option k-sort-handle">
      <k-icon type="sort" />
    </button>
    <k-dropdown>
      <button type="button" class="k-editor-block-option k-editor-block-options-sort" @mousedown.stop="open">
        <k-icon type="angle-down" />
      </button>
      <k-dropdown-content ref="blockOptions" class="k-editor-block-option-dropdown" @mousedown.native.stop @close="onClose">

        <!-- insert -->
        <template v-if="mode === 'insert'">
          <k-dropdown-item
            icon="angle-left"
            class="k-editor-block-option-heading"
            @click="go()"
          >
            {{ $t('editor.options.insert.below') }} …
          </k-dropdown-item>
          <hr>
          <k-dropdown-item
            v-for="definition in blocks"
            :key="definition.type"
            :icon="definition.icon"
            @click="$emit('add', definition.type)"
          >
            {{ definition.label }}
          </k-dropdown-item>
        </template>

        <!-- convert -->
        <template v-else-if="mode === 'convert'">
          <k-dropdown-item
            icon="angle-left"
            class="k-editor-block-option-heading"
            @click="go()"
          >
            {{ $t('editor.options.convert') }} …
          </k-dropdown-item>
          <hr>
          <k-dropdown-item
            v-for="definition in blocks"
            v-if="block.type !== definition.type"
            :key="definition.type"
            :icon="definition.icon"
            @click="$emit('convert', definition.type)"
          >
            {{ definition.label }}
          </k-dropdown-item>
        </template>

        <template v-else>
          <k-dropdown-item icon="add" @click="go('insert')">{{ $t('editor.options.insert.below') }} …</k-dropdown-item>
          <hr>
          <k-dropdown-item v-if="Object.keys(blocks).length > 1" icon="refresh" @click="go('convert')">{{ $t('editor.options.convert') }} …</k-dropdown-item>
          <k-dropdown-item icon="copy" @click="$emit('duplicate')">{{ $t('editor.options.duplicate') }}</k-dropdown-item>
          <hr>
          <template v-if="menuItems.length">
            <k-dropdown-item
              v-for="(menuItem, index) in menuItems"
              :key="index"
              :icon="menuItem.icon"
              :disabled="menuItem.disabled"
              @click="menuItem.click">
              {{ menuItem.label }}
            </k-dropdown-item>
            <hr>
          </template>
          <k-dropdown-item icon="trash" @click="$emit('remove')">{{ $t('editor.options.delete') }}</k-dropdown-item>
        </template>
      </k-dropdown-content>
    </k-dropdown>
  </nav>
</template>

<script>
export default {
  props: {
    blocks: Object,
    block: Object,
    menu: {
      type: Function,
      default() {
        return function () {
          return [];
        };
      }
    },
    sortable: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      mode: null,
      menuItems: []
    };
  },
  methods: {
    open() {
      this.menuItems = this.menu();
      this.$refs.blockOptions.toggle();
      this.$emit("focus");
    },
    close() {
      this.menuItems = [];
      this.$refs.blockOptions.close();
    },
    go(mode) {
      this.mode = mode;
      this.$refs.blockOptions.open();
    },
    onClose() {
      this.mode = null;
    },
  }
};
</script>

<style lang="scss">
@import "variables.scss";

.k-editor-block-options {
  position: absolute;
  left: 0;
  display: flex;
  top: 2px;
  width: 4rem;
  padding: .25rem 0;
  align-items: center;
  justify-content: center;
}
.k-editor-block-option[disabled] {
  visibility: hidden;
  pointer-events: none;
}
.k-editor-block-option {
  cursor: pointer;
}
.k-editor-block-option span {
  display: flex;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 3px;
  align-items: center;
  justify-content: center;
  color: $color-text-lighter;
}
.k-editor-block-option:hover {
  background: rgba(#000, .1);
}
.k-editor-block-option:focus {
  outline: 0;
}
.k-editor-block-options .k-sort-handle {
  padding: 0;
  width: auto;
  height: auto;
}
.k-editor-block-option-dropdown {
  min-width: 15rem;
  margin-bottom: 4.5rem;
}
.k-editor-block-option-heading .k-button-text  {
  opacity: 1;
  font-weight: 600;
}
</style>

<template>
  <div class="k-editor" ref="editor" @mouseleave="onMouseLeave">
    <div v-if="blocks.length" class="k-editor-blocks" :key="modified">
      <div class="k-editor-container">
        <k-draggable :list="blocks" :handle="true" @end="onSort">
          <div
            v-for="(block, index) in blocks"
            v-if="blockTypeExists(block.type)"
            :key="block.id"
            :class="['k-editor-block', 'k-editor-' + block.type + '-block']"
            @click="focus(index)"
            @focusin="onFocus(index)"
            @focusout="onBlur(index)"
            @keydown.meta.d.prevent="duplicate"
            @keydown.meta.shift.o="openOptions(index)"
            @mouseenter="onMouseEnter(index)"
          >
            <k-editor-options
              v-if="showOptions(index)"
              :key="'block-options-' + block.id"
              :ref="'block-options-' + index"
              :blocks="blockTypes"
              :block="blockTypes[block.type]"
              :menu="menu(index)"
              :sortable="sortable"
              @add="add($event)"
              @convert="convertTo($event)"
              @duplicate="duplicate"
              @remove="remove"
              @focus="focus(index)"
            />
            <div class="k-editor-block-container">
              <component
                :attrs="block.attrs"
                :content="block.content"
                :disabled="disabled"
                :endpoints="endpoints"
                :is="'k-editor-' + block.type + '-block'"
                :ref="'block-' + index"
                :spellcheck="spellcheck"
                v-bind="blockTypes[block.type].bind"
                @click.native.stop="closeOptions(index)"
                @append="onAppend(index, $event)"
                @back="onBack(index, $event)"
                @forward="onForward(index, $event)"
                @convert="onConvert(index, $event)"
                @input="onInput(index, $event)"
                @next="onNext"
                @paste="onPaste(index, $event)"
                @prepend="onPrepend(index, $event)"
                @prev="onPrev"
                @remove="onRemove(index, $event)"
                @split="onSplit(index, $event)"
                @update="onUpdate(index, $event)"
              />
            </div>
          </div>
        </k-draggable>
      </div>
    </div>
    <div v-else class="k-editor-placeholder">
      <nav>
        <k-button
          v-for="blockType in blockTypes"
          :disabled="disabled"
          :key="blockType.type"
          :icon="blockType.icon"
          @click="appendAndFocus({ type: blockType.type })"
        >
          {{ blockType.label }}
        </k-button>
      </nav>
    </div>
  </div>
</template>

<script>
import Options from "./Options.vue";

import "./Plugins.js";
import "./Blocks.js";

export default {
  inheritAttrs: false,
  components: {
    "k-editor-options": Options
  },
  blocks: {},
  props: {
    autofocus: Boolean,
    allowed: [Array, Object],
    disabled: Boolean,
    disallowed: [Array, Object],
    endpoints: Object,
    spellcheck: Boolean,
    value: {
      type: [Array, Object],
      default() {
        return []
      }
    }
  },
  beforeCreate() {

    Object.keys(window.editor.blocks).forEach(key => {

      const block = window.editor.blocks[key];

      if (block.extends && window.editor.blocks[block.extends]) {
        block.extends = window.editor.blocks[block.extends];
      }

      this.$options.blocks[key] = {
        label: this.$t("editor.blocks." + key + ".label", block.label || block.type),
        icon: block.icon,
        type: block.type,
        bind: block.bind || {}
      };

      // inject the translated placeholder
      this.$options.blocks[key].bind.placeholder = this.$t("editor.blocks." + key + ".placeholder", block.placeholder || "");

      this.$options.components["k-editor-" + key + "-block"] = block;
    });

  },
  created() {

    this.subscription = this.$store.subscribeAction({
      after: (action, state) => {
        switch (action.type) {
          case "content/revert":
          case "content/create":
            this.blocks   = this.sanitize(this.value);
            this.modified = this.uuid();
        }
      }
    });

    // only include allowed block types
    if (this.allowed && this.allowed.length > 0) {
      Object.keys(this.$options.blocks).forEach(type => {
        if (this.allowed.includes(type) === true) {
          this.blockTypes[type] = this.$options.blocks[type];
        }
      });
    // discard all disallowed block types
    } else if (this.disallowed && this.disallowed.length > 0) {
      this.blockTypes = this.$options.blocks;
      Object.keys(this.$options.blocks).forEach(type => {
        if (this.disallowed.includes(type) === true) {
          this.$delete(this.blockTypes, type);
        }
      });
    } else {
      this.blockTypes = this.$options.blocks;
    }

  },
  destroyed() {
    this.subscription();
  },
  mounted() {
    if (this.autofocus === true) {
      this.$nextTick(this.focus);
    }
  },
  data() {
    const blocks = this.sanitize(this.value);

    return {
      blocks: blocks,
      blockTypes: {},
      focused: 0,
      revalue: false,
      modified: this.uuid(),
      over: null
    };
  },
  computed: {
    focusedBlockDefinition() {
      return this.getFocusedBlockDefinition();
    },
    sortable() {
      return this.blocks.length > 1;
    }
  },
  watch: {
    blocks: {
      handler(blocks) {
        this.$emit("input", blocks);
      },
      deep: true
    }
  },
  methods: {
    add(type) {
      this.appendAndFocus({ type: type }, this.focused);
    },
    append(block, after) {
      block = this.createBlock(block);

      let nextIndex = 0;

      if (after === null || after === undefined) {
        this.blocks.push(block);
        nextIndex = this.blocks.length - 1;
      } else {
        nextIndex = after + 1;
        this.blocks.splice(nextIndex, 0, block);
      }

      return nextIndex;
    },
    appendAndFocus(block, after) {
      const next = this.append(block, after);

      this.$nextTick(() => {
        this.focus(next);
      });
    },
    blockTypeExists(type) {
      if (this.blockTypes && !this.blockTypes[type]) {
        console.log("block component does not exist: " + type);
        return false;
      }

      return true;
    },
    closeOptions(index) {
      const ref = this.$refs["block-options-" + index];

      if (ref && ref[0] && ref[0].close) {
        ref[0].close();
      }
    },
    convertTo(type) {

      if (this.blockTypeExists(type) === false) {
        return false;
      }

      let block          = this.getFocusedBlock();
      let blockComponent = this.getFocusedBlockComponent();

      if (!block) {
        return false;
      }

      if (block.type === type) {
        return true;
      }

      let cursor = 0;

      if (blockComponent.cursorPosition) {
        cursor = blockComponent.cursorPosition();
      }

      block.type = type;
      block.id   = this.uuid();

      this.$nextTick(() => {
        this.focus(this.focused, cursor);
      });

    },
    createBlock(data) {
      const defaults = {
        attrs: {},
        content: "",
        id: this.uuid(),
        type: "paragraph",
      };

      let block = {
        ...defaults,
        ...data
      };

      return block;
    },
    duplicate() {
      const block = JSON.parse(JSON.stringify(this.getFocusedBlock()));

      if (block) {
        block.id = this.uuid();
        this.appendAndFocus(block, this.focused);
      }
    },
    focus(index, cursor) {
      let block = null;

      if (index === null || index === undefined) {
        block = this.getFocusedBlockComponent() || this.getFirstBlockComponent();
      } else {
        block = this.getBlockComponent(index) || this.getFirstBlockComponent();
      }

      if (block && block.focus) {
        try {
          block.focus(cursor);
        } catch (e) {
          // don't throw errors if focusing fails
        }
      }
    },
    getBlock(index) {
      return this.blocks[index];
    },
    getBlockComponent(index) {
      if (index === undefined || index === null) {
        return false;
      }

      const block = this.$refs['block-' + index];

      if (block && block[0]) {
        return block[0];
      }

      return false;
    },
    getBlockDefinition(index) {
      const block = this.getBlock(index);

      if (block && this.blockTypes[block.type]) {
        return this.blockTypes[block.type];
      }
    },
    getBlockTextLength(index) {
      const block = this.getBlockComponent(index);

      if (block && typeof block.length === "function") {
        return block.length();
      }

      return 0;
    },
    getFirstBlockComponent() {
      return this.getBlockComponent(0);
    },
    getLastBlockComponent() {
      return this.getBlockComponent(this.blocks.length - 1);
    },
    getNextBlock(index) {
      return this.blocks[index + 1];
    },
    getNextBlockComponent() {
      return this.getBlockComponent(this.focused + 1);
    },
    getPreviousBlock(index) {
      return this.blocks[index - 1];
    },
    getPreviousBlockComponent() {
      return this.getBlockComponent(this.focused - 1);
    },
    getFocusedBlock() {
      if (this.focused !== null && this.focused !== undefined) {
        return this.blocks[this.focused];
      }
    },
    getFocusedBlockComponent() {
      return this.getBlockComponent(this.focused);
    },
    getFocusedBlockDefinition() {
      if (this.focused !== null && this.focused !== undefined) {
        return this.getBlockDefinition(this.focused);
      }
    },
    importBlocks(blocks) {
      this.blocks = this.blocks.concat(blocks);
    },
    hasNextBlock(index) {
      return this.blocks[index + 1] !== undefined;
    },
    hasPreviousBlock(index) {
      return this.blocks[index - 1] !== undefined;
    },
    menu(index) {
      const component = this.getBlockComponent(index);

      return () => {

        if (component && component.menu) {
          return component.menu();
        }

        return [];

      };
    },
    mergeWithPreviousBlock(index) {

      const block         = this.getBlock(index);
      const previousBlock = this.getPreviousBlock(index);

      if (!block || !previousBlock) {
        return false;
      }

      const previousBlockComponent = this.getPreviousBlockComponent(index);
      const cursorAtEnd            = this.getBlockTextLength(index - 1);

      previousBlock.content += block.content;
      previousBlock.id       = this.uuid();

      this.removeBlock(index);

      this.$nextTick(() => {
        this.focus(index - 1, cursorAtEnd);
      });

    },
    onAppend(index, block) {
      this.appendAndFocus(block, index);
    },
    onBack(index, data) {
      if (data.html.length === 0) {
        this.onRemove(index);
      } else {
        this.mergeWithPreviousBlock(index);
      }
    },
    onBlur(index) {
      this.focused = index;
    },
    onConvert(index, type) {
      if (this.blockTypeExists(type) === false) {
        return false;
      }

      const block = this.getBlockComponent(index);
      const cursor = block.cursorPosition ? block.cursorPosition() : 0;

      this.blocks[index].type = type;
      this.blocks[index].id = this.uuid();

      this.$nextTick(() => {
        this.focus(index, cursor);
      });
    },
    onFocus(index) {
      this.focused = index;
      const block = this.getFocusedBlockComponent();
    },
    onForward(index) {
      this.remove(index);
      this.focus(index + 1, "start");
    },
    onInput(index, data) {
      if (!this.blocks[index]) {
        return false;
      }

      if (data.content !== undefined) {
        this.blocks[index].content = data.content;
      }

      if (data.attrs !== undefined) {
        this.blocks[index].attrs = data.attrs || {};
      }
    },
    onMouseEnter(index) {
      this.over = index;
    },
    onMouseLeave() {
      this.over = null;
    },
    onNext(cursor) {
      if (this.hasNextBlock(this.focused)) {
        this.focus(this.focused + 1, cursor);
      }
    },
    onPaste(index, { html, text }) {

      this.$api
        .post(this.endpoints.field + "/paste", { html })
        .then(blocks => {

          if (blocks.length === 0) {
            return;
          }

          if (blocks.length === 1) {
            const focused = this.getFocusedBlockComponent();
            focused.insertHtml(html);
            return;
          }

          // get the current block
          const block = this.getBlock(index);

          if (block && block.type === "paragraph" && block.content === "") {
            // replace all pasted blocks
            this.blocks.splice(index, 1, ...blocks);
          } else {
            // append all pasted blocks
            this.blocks.splice(index + 1, 0, ...blocks);
          }

        });

    },
    onPrepend(index, block) {
      this.prepend(block, index);
      this.focused = index + 1;
    },
    onPrev(cursor) {
      if (this.hasPreviousBlock(this.focused)) {
        this.focus(this.focused - 1, cursor);
      }
    },
    onRemove(index) {
      this.remove(index);
    },
    onSort(event) {
      const item  = event.item;
      const index = [].indexOf.call(item.parentNode.children, item);

      this.focus(index);
      this.over = index;
    },
    onSplit(index, data) {
      this.split(index, data);
    },
    onUpdate(index, data) {
      let block = this.blocks[index];

      const focused = this.getFocusedBlockComponent();
      const cursor  = focused.cursorPosition ? focused.cursorPosition() : 0;

      this.$set(this.blocks, index, {
        ...block,
        ...data,
        id: this.uuid()
      });

      this.$nextTick(() => {
        this.focus(index, cursor);
      });
    },
    openOptions(index) {
      const ref = this.$refs["block-options-" + index];

      if (ref && ref[0] && ref[0].close) {
        ref[0].open();
      }
    },
    prepend(block, before) {
      block = this.createBlock(block);
      this.blocks.splice(before, 0, block);
      return before;
    },
    prependAndFocus(block, before) {
      const prev = this.prepend(block, before);

      this.$nextTick(() => {
        this.focus(before);
      });
    },
    remove(index) {
      if (index === null || index === undefined) {
        index = this.focused;
      }

      this.removeBlock(index);

      if (this.blocks.length) {
        const previousBlock = this.getPreviousBlockComponent();

        if (previousBlock) {
          this.focused = index - 1;
          previousBlock.focus("end");
        } else {
          this.focused = null;
        }
      }

    },
    removeAll() {
      this.blocks = [];
    },
    removeBlock(index) {
      this.blocks.splice(index, 1);
    },
    removeFocusedBlock() {
      this.removeBlock(this.focused);
    },
    sanitize(blocks) {
      if (blocks.length === 0) {
        return [];
        blocks = [{
          type: "paragraph",
        }];
      }

      // assign a unique ID to each block
      blocks.map(block => {
        block.id = block.id || this.uuid();
        block.attrs = block.attrs || {};
        block.content = block.content || "";
        block.type = this.blockTypeExists(block.type) ? block.type : "paragraph";
        return block;
      });

      return blocks;
    },
    showOptions(index) {

      if (this.disabled) {
        return false;
      }

      if (this.$store.state.drag) {
        return false;
      }

      if (this.over !== index) {
        return false;
      }

      return true;
    },
    split(index, data) {
      let focusedBlock = this.getFocusedBlock();

      this.append({
        type: data.type || focusedBlock.type,
        content: data.after
      }, index);

      focusedBlock.content = data.before;
      focusedBlock.id      = this.uuid();

      this.$nextTick(() => {
        this.focus(index + 1, "start");
      });

    },
    uuid() {
      return '_' + Math.random().toString(36).substr(2, 9);
    }
  }
};
</script>

<style lang="scss">
@import "variables.scss";
.k-field[data-disabled] .k-editor {
  pointer-events: none;
}
.k-editor-placeholder nav {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  grid-gap: .75rem;
}
.k-editor-placeholder .k-button {
  display: flex;
  background: rgba(#000, .075);
  text-align: left;
  padding: .5rem .75rem;
  align-items: center;
  height: 38px;
  white-space: nowrap;
  border-radius: 2px;
  transition: all .2s;
}
.k-editor-placeholder .k-button:hover {
  background: $color-white;
  box-shadow: $box-shadow;
}
.k-editor-container {
  position: relative;
  padding: 1.5rem 0;
  max-width: 50rem;
  margin: 0 auto;
}
.k-editor-blocks {
  position: relative;
  background: $color-white;
  margin-bottom: 1.5rem;
  box-shadow: $box-shadow;
}
.k-editor-block {
  position: relative;
  padding: .325rem 4rem;
}
.k-editor-block.k-sortable-ghost {
  cursor: -webkit-grabbing;
}
.k-editor-block.k-sortable-ghost:before {
  position: absolute;
  content: "";
  top: 0;
  right: 3.5rem;
  bottom: 0;
  left: 3.5rem;
  outline: 2px solid $color-focus;
  background: rgba($color-focus, .125);
}
.k-editor-block.k-sortable-ghost .k-editor-block-options {
  display: none;
}
.k-editor-block.sortable-drag {
  opacity: 0 !important;
  cursor: -webkit-grabbing;
}
.k-editor-block:first-child {
  margin-top: 0;
}
.k-editor-block:last-child {
  margin-bottom: 0;
}
</style>

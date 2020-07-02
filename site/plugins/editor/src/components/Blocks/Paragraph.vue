<script>
import ProseMirror from "../ProseMirror/ProseMirror.vue";

/* Marks */
import Bold from "./Options/Bold.js";
import Code from "./Options/Code.js";
import Italic from "./Options/Italic.js";
import Link from "./Options/Link.js";
import Underline from "./Options/Underline.js";
import StrikeThrough from "./Options/StrikeThrough.js";

const availableOptions = {
  bold: Bold,
  code: Code,
  italic: Italic,
  link: Link,
  strikeThrough: StrikeThrough,
  underline: Underline,
};

export default {
  extends: ProseMirror,
  append: "paragraph",
  icon: "text",
  breaks: true,
  code: false,
  props: {
    attrs: {
      type: Object,
      default() {
        return {}
      }
    }
  },
  methods: {
    onEnter() {
      this.$emit("append", {
        type: this.$options.append
      });
    },
    onInput(html) {
      this.$emit("input", {
        content: html
      });
    },
    onShiftTab() {
      this.$emit("prev");
    },
    onSplit() {
      const cursor = this.cursorPosition();

      if (cursor === 0) {
        this.$emit("prepend");
      } else {
        this.$emit("split", {
          cursor: cursor,
          before: this.htmlBeforeCursor(),
          after: this.htmlAfterCursor(),
          type: this.$options.append
        });
      }
    },
    onTab() {
      this.$emit("next");
    },
  }
};
</script>


import Field from "./components/Field.vue";
import ProseMirror from "./components/ProseMirror/ProseMirror.vue";

panel.plugin("getkirby/editor", {
  components: {
    "k-editable": ProseMirror
  },
  fields: {
    editor: Field
  }
});

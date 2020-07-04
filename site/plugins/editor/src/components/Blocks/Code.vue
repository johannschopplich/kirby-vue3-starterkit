<template>
  <div class="k-editor-code-block-wrapper">
    <k-editable
      ref="editor"
      :breaks="true"
      :code="true"
      :content="content"
      @input="onInput"
    />

    <k-dropdown class="k-editor-code-block-languages">
      <k-button class="k-editor-code-block-languages-toggle" icon="code" @click="$refs.languages.toggle()">{{ selectedLanguage }}</k-button>
      <k-dropdown-content ref="languages" align="right">
        <k-dropdown-item v-for="language in $options.languages" :key="language.id" @click="setLanguage(language.id)">{{ language.label }}</k-dropdown-item>
      </k-dropdown-content>
    </k-dropdown>

  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  icon: "code",
  props: {
    content: String,
    attrs: [Object, Array]
  },
  languages: [
    { id: "bash", label: "Bash" },
    { id: "basic", label: "BASIC" },
    { id: "c", label: "C" },
    { id: "clojure", label: "Clojure" },
    { id: "cpp", label: "C++" },
    { id: "csharp", label: "C#" },
    { id: "css", label: "CSS" },
    { id: "diff", label: "Diff" },
    { id: "elixir", label: "Elixir" },
    { id: "elm", label: "Elm" },
    { id: "erlang", label: "Erlang" },
    { id: "go", label: "Go" },
    { id: "graphql", label: "GraphQL" },
    { id: "haskell", label: "Haskell" },
    { id: "html", label: "HTML" },
    { id: "java", label: "Java" },
    { id: "js", label: "JavaScript" },
    { id: "json", label: "JSON" },
    { id: "latext", label: "LaTeX" },
    { id: "less", label: "Less" },
    { id: "lisp", label: "Lisp" },
    { id: "lua", label: "Lua" },
    { id: "makefile", label: "Makefile" },
    { id: "markdown", label: "Markdown" },
    { id: "markup", label: "Markup" },
    { id: "objectivec", label: "Objective-C" },
    { id: "pascal", label: "Pascal" },
    { id: "perl", label: "Perl" },
    { id: "php", label: "PHP" },
    { id: "text", label: "Plain Text" },
    { id: "python", label: "Python" },
    { id: "r", label: "R" },
    { id: "ruby", label: "Ruby" },
    { id: "rust", label: "Rust" },
    { id: "sass", label: "Sass" },
    { id: "scss", label: "SCSS" },
    { id: "shell", label: "Shell" },
    { id: "sql", label: "SQL" },
    { id: "swift", label: "Swift" },
    { id: "typescript", label: "TypeScript" },
    { id: "vbnet", label: "VB.net" },
    { id: "xml", label: "XML" },
    { id: "yaml", label: "YAML" },
  ],
  data() {
    return {
      language: this.attrs.language
    };
  },
  computed: {
    selectedLanguage() {
      let selectedLanguage = null;
      let currentLanguage  = this.language || "text";

      this.$options.languages.forEach(language => {
        if (language.id === currentLanguage) {
          selectedLanguage = language.label;
        }
      });

      return selectedLanguage;
    }
  },
  methods: {
    focus() {
      this.$refs.editor.focus();
    },
    onInput(html) {
      this.$emit("input", {
        content: html
      });
    },
    setLanguage(language) {
      this.language = language;
      this.$emit("input", {
        attrs: {
          language: language
        }
      });
    }
  }
};
</script>

<style lang="scss">
@import "variables.scss";

.k-editor-code-block {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.k-editor-code-block-wrapper {
  position: relative;
}
.k-editor-code-block-languages {
  position: absolute;
  bottom: 0;
  right: 0;
}
.k-editor-code-block-languages-toggle {
  color: $color-white;
  padding: 0 .5rem .5rem;
  border-radius: 3px;
}
.k-editor-code-block-languages .k-dropdown-content {
  background: $color-white;
  color: $color-black;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  padding: .25rem 0;
  grid-column-gap: 1.5rem;
}
.k-editor-code-block-languages .k-dropdown-content > .k-dropdown-item:first-child,
.k-editor-code-block-languages .k-dropdown-content > .k-dropdown-item:last-child {
  margin: 0;
}
.k-editor-code-block pre {
  background: $color-dark;
  color: #fff;
  padding: 1.5rem;
  border-radius: $border-radius;
}
.k-editor-code-block .k-editor-block-options {
  top: 20px;
}
</style>

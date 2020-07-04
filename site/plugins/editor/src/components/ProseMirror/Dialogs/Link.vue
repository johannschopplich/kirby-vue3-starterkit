<template>
  <k-dialog ref="dialog" size="medium" @submit="submit">
    <k-form
      ref="linkForm"
      :fields="fields"
      v-model="link"
      @submit="submit"
    />
  </k-dialog>
</template>

<script>
export default {
  data() {
    return {
      link: {
        href: null,
        title: null,
        target: false
      }
    };
  },
  computed: {
    fields() {
      return {
        href: {
          label: "URL",
          type: "text",
          icon: "url"
        },
        title: {
          label: "Title",
          type: "text",
          icon: "title"
        },
        target: {
          label: "Open in new window",
          type: "toggle",
          text: ["no", "yes"]
        }
      };
    }
  },
  methods: {
    open(link) {
      this.link = {
        title: null,
        target: false,
        ...link
      };
      this.$refs.dialog.open();
    },
    submit() {
      this.$emit("submit", {
        ...this.link,
        target: this.link.target ? "_blank" : null
      });

      this.$refs.dialog.close();
    }
  }
};
</script>

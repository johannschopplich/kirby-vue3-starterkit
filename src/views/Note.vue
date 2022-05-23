<template>
  <article v-if="page.isReady" class="note">
    <header class="note-header intro">
      <h1>{{ page.title }}</h1>
      <time class="note-date">{{ page.date }}</time>
      <p class="note-tags tags">
        {{ page.tags }}
      </p>
    </header>

    <!-- eslint-disable-next-line vue/no-v-html -->
    <div v-kirbytext class="note-text text" v-html="page.text" />
  </article>
</template>

<script setup>
import { usePage } from "~/composables";

const page = usePage();
</script>

<style scoped>
.note {
  max-width: 35rem;
  margin: 0 auto;
}
.note-header {
  text-align: center;
}
.note-date {
  margin-bottom: 0.5rem;
  display: block;
}

::v-deep(.gallery) {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
  grid-gap: 1.5rem;
  margin-bottom: 1.5rem;
  padding: 3rem 0;
}
::v-deep(.gallery figure a) {
  border: 0;
}
::v-deep(.gallery figure) {
  margin: 0;
  padding: 0;
}

@media screen and (min-width: 45rem) {
  ::v-deep(.gallery) {
    margin-left: -3rem;
    margin-right: -3rem;
  }
}
</style>

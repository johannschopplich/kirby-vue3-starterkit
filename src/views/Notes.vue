<template>
  <Intro>{{ page.title }}</Intro>

  <div v-if="page.isReady" class="notes">
    <article v-for="note in page.children" :key="note.uri" class="note">
      <header class="note-header">
        <router-link :to="`/${note.uri}`">
          <h2>{{ note.title }}</h2>
          <time>{{ note.date }}</time>
        </router-link>
      </header>
    </article>
  </div>
  <div v-else>Loading …</div>
</template>

<script>
import { usePage } from "~/composables";

export default {
  setup() {
    return {
      page: usePage(),
    };
  },
};
</script>

<style scoped>
.notes {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
  grid-gap: 1.5rem;
  grid-auto-rows: 1fr;
}
.note {
  border: 2px solid #000;
}
.note a {
  display: block;
  padding: 1rem;
  line-height: 1.25em;
}
.note h2 {
  font-size: 1rem;
}
.note time {
  font-size: 0.75rem;
}
</style>

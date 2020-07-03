<template>
  <!-- eslint-disable vue/no-v-html -->
  <main id="main">
    <Intro :title="page.title" />

    <div class="notes">
      <article v-for="note in page.children" :key="note.id" class="note">
        <header class="note-header">
          <router-link :to="`/${note.id}`">
            <h2>{{ note.title }}</h2>
            <time>{{ note.date }}</time>
          </router-link>
        </header>
      </article>
    </div>
  </main>
</template>

<script>
import Intro from '../components/Intro.vue'
import { usePage } from '../hooks/usePage'

export default {
  name: 'Notes',
  components: { Intro },

  setup () {
    const page = usePage()
    return { page }
  }
}
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
  font-size: .75rem;
}
</style>

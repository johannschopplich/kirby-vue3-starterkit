<template>
  <Intro :title="page.title" />

  <div v-if="page.text" class="text" v-html="page.text.html" />
</template>

<script>
import Intro from '../components/Intro.vue'
import { usePage } from '../hooks/usePage'

export default {
  name: 'Default',
  components: { Intro },

  setup () {
    return {
      page: usePage()
    }
  }
}
</script>

<style scoped>
.text {
  max-width: 35rem;
  margin: 0 auto;
}
</style>

<template>
  <Intro :title="page.title" />

  <div v-if="page.text" class="text" v-html="page.text.html" v-kirbytext />
</template>

<script>
import { usePage } from '../hooks/usePage'
import Intro from '../components/Intro.vue'

export default {
  components: {
    Intro
  },

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

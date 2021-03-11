<template>
  <Intro>{{ page.title }}</Intro>

  <div
    v-if="page.isReady"
    v-kirbytext
    class="text"
    v-html="page.text"
  />
</template>

<script>
import { usePage } from '~/hooks'
import Intro from '~/components/Intro.vue'

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

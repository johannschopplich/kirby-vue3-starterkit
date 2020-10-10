<template>
  <Intro>{{ page.title }}</Intro>

  <div v-if="page.isReady" class="text" v-html="page.text" v-kirbytext />
</template>

<script setup>
import { usePage } from '../hooks/usePage'

export { default as Intro } from '../components/Intro.vue'
export const page = usePage()
</script>

<style scoped>
.text {
  max-width: 35rem;
  margin: 0 auto;
}
</style>

<template>
  <Intro>{{ page.title }}</Intro>

  <!-- eslint-disable-next-line vue/no-v-html -->
  <div v-if="page.isReady" v-kirbytext class="text" v-html="page.text" />
</template>

<script setup>
import { usePage } from "~/composables";

const page = usePage();
</script>

<style scoped>
.text {
  max-width: 35rem;
  margin: 0 auto;
}
</style>

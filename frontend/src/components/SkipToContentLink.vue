<template>
  <a href="#main" class="skip-to-content-link" ref="skipLink">
    Skip to content
  </a>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'

export const skipLink = ref(null)

onMounted(() => {
  const route = useRoute()
  watch(route, () => {
    skipLink.value.focus()
  })
})
</script>

<style>
.skip-to-content-link {
  position: absolute;
  top: 0;
  left: 50%;
  padding: .5rem;
  transform: translate(-50%, 0);
}
.skip-to-content-link:not(:focus) {
  transform: translate(-50%, -100%);
}
</style>

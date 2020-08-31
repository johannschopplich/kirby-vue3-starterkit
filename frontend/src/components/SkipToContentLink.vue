<template>
  <a
    href="#main"
    class="skip-to-content-link"
    ref="skipLink"
    @click.prevent="handleFocusElement"
  >
    Skip to content
  </a>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const focusElement = id => {
  const element = document.querySelector(id)
  if (!id || !element) return
  element.scrollIntoView()
  element.focus()
}

export const skipLink = ref(null)
export const handleFocusElement = ({ target }) => {
  focusElement(target.hash)
}

const route = useRoute()
watch(route, () => {
  skipLink.value.focus()
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

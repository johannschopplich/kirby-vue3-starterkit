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

const skipLink = ref(null)
const route = useRoute()

watch(route, () => {
  skipLink.value.focus()
})

const focusElement = id => {
  const element = document.querySelector(id)
  if (!id || !element) return
  element.scrollIntoView()
  element.focus()
}

const handleFocusElement = ({ target }) => {
  focusElement(target.hash)
}

export {
  skipLink,
  handleFocusElement
}
</script>

<style>
.skip-to-content-link {
  position: absolute;
  top: 0;
  left: 50%;
  padding: .5rem;
  transform: translate(-50%, -100%);
}
.skip-to-content-link:focus {
  transform: translate(-50%, 0);
  /* Copy the browser's native focus styles */
  outline: 5px auto Highlight;
  outline: 5px auto -webkit-focus-ring-color;
}
</style>

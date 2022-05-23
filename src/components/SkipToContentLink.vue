<template>
  <a
    ref="skipLink"
    href="#main"
    class="skip-to-content-link"
    @click.prevent="navigate()"
  >
    Skip to content
  </a>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const skipLink = ref();

watch(route, () => {
  skipLink.value.focus();
});

function navigate({ target }) {
  const { hash } = target;
  if (!hash) return;

  const element = document.querySelector(hash);
  element?.scrollIntoView();
  element?.focus();
}
</script>

<style>
.skip-to-content-link {
  position: absolute;
  top: 0;
  left: 50%;
  padding: 0.5rem;
  transform: translate(-50%, -100%);
}
.skip-to-content-link:focus {
  transform: translate(-50%, 0);
  /* Copy the browser's native focus styles */
  outline: 5px auto Highlight;
  outline: 5px auto -webkit-focus-ring-color;
}
</style>

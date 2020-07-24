<template>
  <a href="#main" class="skip-to-content-link" ref="skipLink">
    Skip to content
  </a>
</template>

<script>
export default {
  watch: {
    $route () {
      this.$refs.skipLink.focus()
    }
  }
}
</script>

<style>
.skip-to-content-link {
  position: absolute;
  top: 0;
  left: 50%;
  padding: .5rem;
  transform: translate(-50%, .25rem);
}
.skip-to-content-link:not(:focus) {
  transform: translate(-50%, -100%);
}
</style>

<template>
  <router-link to="#main" class="skip-to-content-link">
    Skip to content
  </router-link>
</template>

<style>
.skip-to-content-link {
  position: absolute;
  top: -2px;
  left: 50%;
  border: 2px solid #000;
  padding: .5rem 1rem;
  transform: translate(-50%, 0%);
}
.skip-to-content-link:not(:focus) {
  transform: translate(-50%, -100%);
}
</style>

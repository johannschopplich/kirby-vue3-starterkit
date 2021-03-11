<template>
  <aside aria-live="polite">
    <button
      v-if="hasNewWorker"
      id="update-notification"
      @click="activateNewWorker"
    >
      New app version available. Click to install.
    </button>
  </aside>
</template>

<script>
import { useServiceWorker } from '~/hooks'

export default {
  setup () {
    const { hasNewWorker, activateNewWorker } = useServiceWorker()
    return { hasNewWorker, activateNewWorker }
  }
}
</script>

<style>
#update-notification {
  -webkit-appearance: button;
  position: fixed;
  bottom: 1.5rem;
  left: 50%;
  background: white;
  border: 2px solid #000;
  font-family: inherit;
  font-size: 1rem;
  padding: .5rem 1rem;
  margin: 0;
  white-space: nowrap;
  transform: translate(-50%, 0%);
  cursor: pointer;
  z-index: 10;
}
#update-notification:hover,
#update-notification:focus {
  background: #000;
  color: #fff;
}
</style>

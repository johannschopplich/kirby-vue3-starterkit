<template>
  <div
    id="announcer"
    class="announcer"
    :aria-live="announcer.politeness"
    v-text="announcer.content"
  />
</template>

<script>
import { useAnnouncer } from '../hooks/useAnnouncer'

export default {
  name: 'Announcer',

  setup () {
    const { announcer } = useAnnouncer()

    return {
      announcer
    }
  }
}
</script>

<style scoped>
.announcer {
  position: absolute;
  width: 1px;
  height: 1px;
  border: 0;
  padding: 0;
  margin: 0;
  clip-path: inset(50%);
  overflow: hidden;
  white-space: nowrap;
}
</style>

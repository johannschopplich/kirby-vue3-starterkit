<template>
  <div
    id="announcer"
    :aria-live="announcer.politeness"
    v-text="announcer.content"
  />
</template>

<script>
import { useAnnouncer } from '~/hooks'

export default {
  setup () {
    const { announcer } = useAnnouncer()
    return { announcer }
  }
}

</script>

<style>
#announcer {
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

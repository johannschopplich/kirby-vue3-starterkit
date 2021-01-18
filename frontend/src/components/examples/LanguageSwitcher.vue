<template>
  <a
    v-for="lang in site.languages.filter(language => language.code !== currentCode)"
    :key="lang.code"
    :href="`/${lang.code}/${route.path}`"
  >
    {{ lang.name }}
  </a>
</template>

<script>
import { useRoute } from 'vue-router'
import { useSite, useLanguages } from '../hooks'

export default {
  setup () {
    const site = useSite()
    const route = useRoute()
    const { languageCode: currentCode } = useLanguages()

    return {
      site,
      route,
      currentCode
    }
  }
}
</script>

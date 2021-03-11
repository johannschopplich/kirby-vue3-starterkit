<template>
  <a
    v-for="language in languages"
    :key="language.code"
    :href="`/${language.code}${route.path}`"
  >
    {{ language.name }}
  </a>
</template>

<script>
import { useRoute } from 'vue-router'
import { useSite, useLanguages } from '~/hooks'

export default {
  setup () {
    const site = useSite()
    const route = useRoute()
    const { languageCode: currentCode } = useLanguages()
    const languages = site.languages.filter(({ code }) => code !== currentCode)

    return {
      route,
      languages
    }
  }
}
</script>

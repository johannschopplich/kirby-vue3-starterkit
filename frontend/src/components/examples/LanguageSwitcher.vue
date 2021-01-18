<template>
  <a
    v-for="lang in site.languages.filter(language => language.code !== currentCode)"
    :key="lang.code"
    :href="`/${lang.code}`"
  >
    {{ lang.name }}
  </a>
</template>

<script>
import { useSite, useLanguages } from '../hooks'

export default {
  setup () {
    const site = useSite()
    const { languageCode: currentCode } = useLanguages()

    return {
      site,
      currentCode
    }
  }
}
</script>

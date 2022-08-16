<template>
  <a
    v-for="language in languages"
    :key="language.code"
    :href="`/${language.code}${route.path}`"
  >
    {{ language.name }}
  </a>
</template>

<script setup>
import { useRoute } from "vue-router";
import { useLanguages, useSite } from "~/composables";

const site = useSite();
const route = useRoute();
const { languageCode: currentCode } = useLanguages();
const languages = site.languages.filter(({ code }) => code !== currentCode);
</script>

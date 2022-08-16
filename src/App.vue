<template>
  <SkipToContentLink />
  <Announcer />

  <div class="page">
    <Header />
    <main id="main" tabindex="-1">
      <RouterView :key="route.path" />
    </main>
  </div>

  <Footer />
</template>

<script setup>
import { useRoute } from "vue-router";
const route = useRoute();
</script>

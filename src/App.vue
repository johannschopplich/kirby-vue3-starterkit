<template>
  <SkipToContentLink />
  <Announcer />

  <div class="page">
    <Header />
    <main id="main" tabindex="-1">
      <router-view :key="route.path" />
    </main>
  </div>

  <UpdateNotification />
  <Footer />
</template>

<script setup>
import { useRoute } from "vue-router";
const route = useRoute();
</script>

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

<script>
import { useRoute } from "vue-router";

export default {
  setup() {
    return {
      route: useRoute(),
    };
  },
};
</script>

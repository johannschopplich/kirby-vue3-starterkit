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
import { useRoute } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import Announcer from './components/Announcer.vue'
import SkipToContentLink from './components/SkipToContentLink.vue'
import UpdateNotification from './components/UpdateNotification.vue'

export default {
  components: {
    Header,
    Footer,
    Announcer,
    SkipToContentLink,
    UpdateNotification
  },

  setup () {
    return {
      route: useRoute()
    }
  }
}
</script>

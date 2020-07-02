<template>
  <router-link
    to="#main"
    class="skip-to-content-link"
    @click.native="scrollToHash('#main')"
  >
    Skip to content
  </router-link>

  <div class="page">
    <Header />

    <!-- TODO: Use `keep-alive` once Vue Router 4 supports it -->
    <!-- <keep-alive> -->
      <router-view :key="path" />
    <!-- </keep-alive> -->
  </div>

  <Footer />
</template>

<script>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'App',
  components: { Header, Footer },

  setup () {
    const { path, hash } = useRoute()

    const scrollToHash = hash => {
      if (hash) window.location.hash = hash
    }

    onMounted(() => {
      scrollToHash(hash)
    })

    return {
      path,
      scrollToHash
    }
  }
}
</script>

<style>
:root {
  --content-width: 65rem;
}

*,
*:after,
*:before {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji';
}

.skip-to-content-link {
  position: absolute;
  top: -1px;
  left: 50%;
  border: 1px solid #000;
  padding: .25rem .5rem;
  transform: translate(-50%, -100%);
}
.skip-to-content-link:focus {
  transform: translate(-50%, 0%);
}

li {
  list-style: none;
}

a {
  color: currentColor;
  text-decoration: none;
}

strong,
b {
  font-weight: 500;
}

img {
  width: 100%;
}

.page {
  padding: 5vh 5vw 10vh;
}
.page > * {
  max-width: var(--content-width);
  margin: 0 auto;
}

main {
  min-height: calc(100vh - 10rem);
}

.tags {
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.075em;
  font-size: 0.75rem;
  font-weight: 600;
}

.text {
  line-height: 1.5em;
}
.text p,
.text figure,
.text ul,
.text ol {
  margin-bottom: 1.5em;
}
.text h2 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-top: 3rem;
  margin-bottom: 1.5rem;
  text-align: center;
}
.text > *:first-child {
  margin-top: 0;
}
.text a {
  position: relative;
  white-space: nowrap;
  font-weight: 500;
  z-index: 1;
  display: inline-block;
  border-bottom: 2px solid #000;
}
.text figure {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}
.text img {
  width: 100%;
}
</style>

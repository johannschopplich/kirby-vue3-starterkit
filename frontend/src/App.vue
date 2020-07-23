<template>
  <router-link to="#main" class="skip-link">
    Skip to content
  </router-link>
  <button class="app-update-notification">
    New app version available. Click to install.
  </button>

  <div class="page">
    <Header />
    <router-view />
  </div>

  <Footer />
</template>

<script>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'

export default {
  name: 'App',
  components: { Header, Footer }
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

.skip-link {
  position: absolute;
  top: -4px;
  left: 50%;
  border: 2px solid #000;
  padding: .5rem 1rem;
  transform: translate(-50%, -100%);
}
.skip-link:focus {
  transform: translate(-50%, 0%);
}

.app-update-notification {
  -webkit-appearance: button;
  position: fixed;
  bottom: 4px;
  left: 50%;
  background: white;
  border: 2px solid #000;
  font-family: inherit;
  font-size: 1rem;
  padding: .5rem 1rem;
  margin: 0;
  white-space: nowrap;
  transform: translate(-50%, 0%);
  cursor: pointer;
  z-index: 10;
}
.app-update-notification:not(.show) {
  transform: translate(-50%, calc(100% + 4px));
}
.app-update-notification:hover,
.app-update-notification:focus {
  background: #000;
  color: #fff;
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
  letter-spacing: .075em;
  font-size: .75rem;
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

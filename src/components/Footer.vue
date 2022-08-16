<template>
  <footer class="footer">
    <router-link to="/">
      &copy; {{ new Date().getFullYear() }} / {{ site.title }}
    </router-link>

    <nav class="social">
      <a v-for="social in site.social" :key="social.url" :href="social.url">
        {{ social.platform }}
      </a>
    </nav>
  </footer>
</template>

<script setup>
import { useSite } from "~/composables";

const site = useSite();
</script>

<style scoped>
.footer {
  padding: 1.5rem 5vw 10vh;
  text-align: center;
  max-width: var(--content-width);
  margin: 0 auto;
  line-height: 1.5em;
}
.footer a {
  display: inline-block;
  font-size: 0.875rem;
}
.footer > a {
  margin-bottom: 1.5rem;
  border-top: 2px solid #000;
  width: 16.5rem;
  padding-top: 0.5rem;
}

.social a {
  margin: 0 0.75rem;
  padding: 0.5rem 1rem;
  border: 2px solid #000;
  width: 7.5rem;
}
.social a:hover {
  background: #000;
  color: #fff;
}
</style>

<template>
  <Intro>{{ page.title }}</Intro>

  <div v-if="page.isReady" class="layout">
    <aside>
      <section>
        <h2>Address</h2>
        <!-- eslint-disable-next-line vue/no-v-html -->
        <div class="text" v-html="page.address" />
      </section>

      <section>
        <h2>Email</h2>
        <div class="text">
          <a :href="`mailto:${page.email}`">{{ page.email }}</a>
        </div>
      </section>

      <section>
        <h2>Phone</h2>
        <div class="text">
          <a :href="`tel:${page.phone}`">{{ page.phone }}</a>
        </div>
      </section>

      <section>
        <h2>On the web</h2>
        <div class="text">
          <ul>
            <li v-for="social in page.social" :key="social.uri">
              <a :href="social.url">{{ social.platform }}</a>
            </li>
          </ul>
        </div>
      </section>
    </aside>

    <!-- eslint-disable-next-line vue/no-v-html -->
    <div v-kirbytext class="text" v-html="page.text" />
  </div>
</template>

<script setup>
import { usePage } from "~/composables";

const page = usePage();
</script>

<style>
.layout {
  display: grid;
  grid-template-columns: 1fr;
  grid-gap: 3rem;
}

@media screen and (min-width: 45rem) {
  .layout {
    grid-template-columns: 1fr 2fr;
  }
}

.layout aside section {
  margin-bottom: 3rem;
}

.layout aside h2 {
  margin-bottom: 0.75rem;
}
</style>

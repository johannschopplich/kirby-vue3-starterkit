<template>
  <main id="main">
    <Intro :title="page.title" />

    <div class="layout">
      <aside>
        <section>
          <h2>Address</h2>
          <div v-if="page.address" class="text" v-html="page.address.html" />
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
              <li v-for="social in page.social" :key="social.id">
                <a :href="social.url">{{ social.platform }}</a>
              </li>
            </ul>
          </div>
        </section>
      </aside>

      <div v-if="page.text" class="text" v-html="page.text.html" />
    </div>
  </main>
</template>

<script>
import Intro from '../components/Intro.vue'
import { usePage } from '../hooks/usePage'

export default {
  name: 'About',
  components: { Intro },

  setup () {
    return {
      page: usePage()
    }
  }
}
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
  margin-bottom: .75rem;
}
</style>

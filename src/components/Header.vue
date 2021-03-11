<template>
  <header class="header">
    <router-link to="/" class="logo">
      {{ site.title }}
    </router-link>

    <nav id="menu" class="menu">
      <router-link
        v-for="page in site.children.filter(page => page.isListed)"
        :key="page.uri"
        :to="`/${page.uri}`"
        :class="{ 'router-link-active': route.path.startsWith(`/${page.uri}/`) }"
      >
        {{ page.title }}
      </router-link>
    </nav>
  </header>
</template>

<script>
import { useRoute } from 'vue-router'
import { useSite } from '~/hooks'

export default {
  setup () {
    const site = useSite()
    const route = useRoute()
    return { site, route }
  }
}
</script>

<style>
.header {
  margin-bottom: 1.5rem;
}

.header a {
  position: relative;
  text-transform: uppercase;
  font-size: .875rem;
  letter-spacing: .05em;
  padding: .5rem 0;
  font-weight: 700;
}

.header .logo {
  display: block;
  margin-bottom: 1.5rem;
  padding: .5rem 0;
}

.header {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.menu a {
  margin: 0 .75rem;
}

.menu a[aria-current="page"],
.menu a.router-link-active {
  border-bottom: 2px solid #000;
}

@media screen and (min-width: 40rem) {
  .header .logo {
    margin-bottom: 0;
  }
  .header {
    flex-direction: row;
    justify-content: space-between;
  }
  .menu {
    margin-right: -.75rem;
  }
}
</style>

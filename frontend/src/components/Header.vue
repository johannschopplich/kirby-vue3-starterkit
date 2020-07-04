<template>
  <header class="header">
    <router-link v-slot="{ href, isExactActive, navigate }" to="/">
      <a
        :href="href"
        :aria-current="isExactActive ? 'page' : false"
        class="logo"
        @click="navigate"
      >
        {{ site.title }}
      </a>
    </router-link>

    <nav id="menu" class="menu">
      <router-link
        v-for="page in site.children.filter(page => page.isListed)"
        v-slot="{ href, isExactActive, navigate }"
        :key="page.id"
        :to="`/${page.id}`"
      >
        <a
          :href="href"
          :aria-current="isExactActive ? 'page' : false"
          @click="navigate"
        >
          {{ page.title }}
        </a>
      </router-link>
    </nav>
  </header>
</template>

<script>
import { useSite } from '../hooks/useSite'

export default {
  name: 'Header',

  setup () {
    return {
      site: useSite()
    }
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

.menu a[aria-current="page"] {
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

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
        @click.native="scrollToTop"
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
export default {
  name: 'Header',

  setup () {
    const scrollToTop = () => {
      window.scrollTo(0, 0)
    }

    return {
      scrollToTop,
      site: window.$site
    }
  }
}
</script>

<style scoped>
.header {
  margin-bottom: 1.5rem;
}

.header a {
  position: relative;
  text-transform: uppercase;
  font-size: 0.875rem;
  letter-spacing: 0.05em;
  padding: 0.5rem 0;
  font-weight: 700;
}

.header .logo {
  display: block;
  margin-bottom: 1.5rem;
  padding: 0.5rem 0;
}

.header {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.menu a {
  margin: 0 0.75rem;
}

.menu a[aria-current],
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
    margin-right: -0.75rem;
  }
}
</style>

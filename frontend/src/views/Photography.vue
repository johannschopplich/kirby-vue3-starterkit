<template>
  <main id="main">
    <Intro :title="page.title" />

    <ul v-if="page.children" class="albums" :data-even="page.children.length % 2 === 0">
      <li v-for="album in page.children" :key="album.id">
        <router-link :to="`/${album.id}`">
          <figure>
            <img :src="album.cover.url" :alt="album.cover.alt">

            <figcaption>{{ album.title }}</figcaption>
          </figure>
        </router-link>
      </li>
    </ul>
  </main>
</template>

<script>
import Intro from '../components/Intro.vue'
import { usePage } from '../hooks/page'

export default {
  name: 'Photography',
  components: { Intro },

  setup () {
    const { page } = usePage()
    return { page }
  }
}
</script>

<style>
.albums {
  display: grid;
  list-style: none;
  grid-gap: 1rem;
  line-height: 0;
}

@media screen and (min-width: 30em) {
  .albums {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media screen and (min-width: 60em) {
  .albums {
    grid-template-columns: repeat(3, 1fr);
  }
  .albums[data-even] {
    grid-template-columns: repeat(4, 1fr);
  }
}

.albums li {
  overflow: hidden;
  background: #000;
}
.albums figure {
  position: relative;
  padding-bottom: 125%;
}
.albums figcaption {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  color: #fff;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  line-height: 1.5em;
  padding: 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.125em;
}
.albums img {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s;
}
.albums img:hover {
  opacity: 0.2;
}
</style>

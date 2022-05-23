<template>
  <article v-if="page.isReady">
    <header>
      <figure class="album-cover">
        <img :src="page.cover.url" :alt="page.cover.alt" />

        <figcaption>
          <h1>{{ page.headline }}</h1>
        </figcaption>
      </figure>
    </header>

    <div class="album-text text">
      <!-- eslint-disable-next-line vue/no-v-html -->
      <span v-html="page.description" />
      <p class="album-tags tags">
        {{ page.tags }}
      </p>
    </div>

    <ul
      class="album-gallery"
      :data-even="page.gallery.length % 2 === 0"
      :data-count="page.gallery.length"
    >
      <li v-for="image in page.gallery" :key="image.url">
        <figure>
          <a :href="image.link">
            <img :src="image.url" :alt="image.alt" />
          </a>
        </figure>
      </li>
    </ul>
  </article>
</template>

<script setup>
import { usePage } from "~/composables";

const page = usePage();
</script>

<style>
.album-cover {
  position: relative;
  line-height: 0;
  margin-bottom: 6rem;
  background: #000;
  padding-bottom: 75%;
}
.album-cover figcaption {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  bottom: 0;
  left: 0;
  right: 0;
  top: 0;
  background: rgba(0, 0, 0, 0.5);
  text-align: center;
  color: #fff;
  line-height: 1;
  padding: 1.5rem;
}
.album-cover img {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.album-cover h1 {
  font-size: 3rem;
}
.album-text {
  max-width: 40rem;
  margin: 0 auto 6rem;
  text-align: center;
}
.album-gallery {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  align-items: center;
  margin: 0 auto;
  grid-gap: 1rem;
  max-width: calc(var(--content-width) - 15rem);
  justify-content: center;
}
.album-gallery[data-even] {
  grid-template-columns: repeat(4, 1fr);
}
.album-gallery[data-count="1"] {
  grid-template-columns: 1fr;
}
.album-gallery[data-count="2"] {
  grid-template-columns: 1fr 1fr;
}
</style>

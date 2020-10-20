<template>
  <header class="intro">
    <h1><slot /></h1>
  </header>
</template>

<style>
.intro {
  padding: 10vh 0;
  text-align: center;
}

.intro h1 {
  position: relative;
  margin-bottom: 1rem;
  font-weight: 900;
  font-size: calc(1vw + 2rem);
  z-index: 1;
}
</style>

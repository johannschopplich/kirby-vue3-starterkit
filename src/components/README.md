# Components

Components will be automatically imported on demand thanks to [unplugin-vue-components](https://github.com/antfu/unplugin-vue-components). Thus, no need to import and register your components manually anymore! If you register the parent component asynchronously (or via a lazy route), the auto imported components will be code-split along with their parent.

Use your component in your template as you normally would:

```vue
<template>
  <div>
    <Intro>Headline</Intro>
  </div>
</template>
```

… Which gets transpiled by Vite on the fly to:

```vue
<template>
  <div>
    <Intro>Headline</Intro>
  </div>
</template>

<script>
import Intro from "../components/Intro.vue";

export default {
  components: {
    Intro,
  },
};
</script>
```

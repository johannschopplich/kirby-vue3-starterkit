# Modules

A custom user module system. Every `.js` file inside this folder following the template below will be installed automatically.

```js
/** @param {import("vue").App} app */
export const install = (app) => {
  // Do something with `app`, like `app.use`
};
```

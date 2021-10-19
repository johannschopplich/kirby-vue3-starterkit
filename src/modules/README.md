# Modules

> A custom user module system.

Every `.js` file inside this folder following the template below will be installed automatically. Thus, no need to edit the `main.js` entry point of the app anymore.

```js
/** @param {import("vue").App} app */
export const install = (app) => {
  // Do something with `app`, like `app.use`
};
```

import { resolve } from "path";
import { defineConfig, loadEnv } from "vite";
import Vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";

const root = "src";

export default ({ mode }) => {
  Object.assign(
    process.env,
    loadEnv(mode, process.cwd(), ["VITE_", "KIRBY_", "CONTENT_"])
  );

  process.env.VITE_BACKEND_URL = `${process.env.KIRBY_DEV_PROTOCOL}://${process.env.KIRBY_DEV_HOSTNAME}:${process.env.KIRBY_DEV_PORT}`;
  process.env.VITE_BACKEND_API_SLUG = process.env.CONTENT_API_SLUG;
  process.env.VITE_MULTILANG = process.env.KIRBY_MULTILANG;

  return defineConfig({
    root,
    base: mode === "development" ? "/" : "/dist/",

    resolve: {
      alias: {
        "~/": `${resolve(__dirname, root)}/`,
      },
    },

    build: {
      outDir: resolve(__dirname, "public/dist"),
      emptyOutDir: true,
      manifest: true,
      rollupOptions: {
        input: resolve(root, "main.js"),
      },
    },

    plugins: [
      Vue(),

      Components({
        dirs: ["components"],
      }),
    ],

    server: {
      cors: true,
      host: "0.0.0.0",
      port: process.env.VITE_DEV_PORT,
      strictPort: true,
    },
  });
};

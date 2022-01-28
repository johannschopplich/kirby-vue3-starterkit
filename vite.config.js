import "dotenv/config";
import { defineConfig } from "vite";
import { resolve } from "path";
import Components from "unplugin-vue-components/vite";
import Vue from "@vitejs/plugin-vue";

process.env.VITE_BACKEND_URL = `${process.env.KIRBY_DEV_PROTOCOL}://${process.env.KIRBY_DEV_HOSTNAME}:${process.env.KIRBY_DEV_PORT}`;
process.env.VITE_BACKEND_API_SLUG = process.env.CONTENT_API_SLUG;
process.env.VITE_MULTILANG = process.env.KIRBY_MULTILANG;

const root = "src";

export default defineConfig(({ mode }) => ({
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
    port: process.env.VITE_DEV_PORT,
    host: "0.0.0.0",
    strictPort: true,
  },

  optimizeDeps: {
    include: ["vue", "vue-router", "change-case"],
  },
}));

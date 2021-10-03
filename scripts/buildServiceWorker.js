// @ts-check
/* eslint-env node */

require("dotenv").config();
const { readFile, writeFile } = require("fs/promises");
const { transform } = require("esbuild");
const { green } = require("colorette");
const { nanoid } = require("nanoid");

const swSrcPath = "src/serviceWorker.js";
const swDistPath = "public/service-worker.js";

/**
 * Main entry point
 */
async function main() {
  if (process.env.VITE_SERVICE_WORKER !== "true") return;

  console.log(green("Building service worker..."));

  const swManifest = JSON.parse(
    await readFile("public/dist/manifest.json", "utf-8")
  );

  const assets = Object.values(swManifest).map((i) => `/dist/${i.file}`);
  const bundle = `
    self.__PRECACHE_MANIFEST = [${assets.map((i) => `"${i}"`).join(",")}]
    const VERSION = "${nanoid()}"
    const KIRBY_API_SLUG = "${process.env.KIRBY_API_SLUG || "api"}"
    const CONTENT_API_SLUG = "${process.env.CONTENT_API_SLUG}"
    ${await readFile(swSrcPath)}
  `;

  const { code } = await transform(bundle, { minify: true });
  await writeFile(swDistPath, code);

  console.log(
    `${green("✓")} Added ${
      assets.length
    } additional service worker assets to precache.`
  );
}

main().catch(() => process.exit(1));

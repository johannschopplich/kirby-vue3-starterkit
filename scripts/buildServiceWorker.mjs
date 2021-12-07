// @ts-check

import "dotenv/config";
import { readFile, writeFile } from "fs/promises";
import { transform } from "esbuild";
import { nanoid } from "nanoid";
import consola from "consola";

const srcPath = "src/serviceWorker.js";
const distPath = "public/service-worker.js";

async function main() {
  if (process.env.VITE_SERVICE_WORKER !== "true") return;

  consola.start("building service worker...");

  const swManifest = JSON.parse(
    await readFile("public/dist/manifest.json", "utf-8")
  );

  const assets = Object.values(swManifest).map((i) => `/dist/${i.file}`);
  const bundle = `
    self.__PRECACHE_MANIFEST = [${assets.map((i) => `"${i}"`).join(",")}]
    const VERSION = "${nanoid()}"
    const KIRBY_API_SLUG = "${process.env.KIRBY_API_SLUG || "api"}"
    const CONTENT_API_SLUG = "${process.env.CONTENT_API_SLUG}"
    ${await readFile(srcPath)}
  `;

  const { code } = await transform(bundle, { minify: true });
  await writeFile(distPath, code);

  consola.success(`${assets.length} assets added to precache`);
}

main().catch((err) => consola.error(err));

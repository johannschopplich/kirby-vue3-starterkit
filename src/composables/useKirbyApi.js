import { $fetch } from "ohmyfetch";
import { useLanguages } from "./";

const cache = new Map();

/**
 * Builds an API URL for a specific file and language
 *
 * @param {string} path The path to the file desired
 * @returns {string} The final URL
 */
function getApiUri(path) {
  const { isMultilang, languageCode } = useLanguages();
  let result = "";

  // Add language path in multi-language setup
  if (isMultilang) {
    result += `/${languageCode}`;
  }

  // Add the API path
  result += `/${import.meta.env.VITE_BACKEND_API_SLUG}`;

  // Add the file path to fetch
  result += `/${path}`;

  return result;
}

/**
 * Retrieves page data by id from either network or store
 *
 * @param {string} id The page to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} [options.revalidate=false] Skip cache look-up and fetch page freshly
 * @param {string} [options.token] Add a token to the request to fetch a draft preview
 * @returns {Promise<Record<string, any>|boolean>} The page's data or `false` if fetch request failed
 */
async function getPage(id, { revalidate = false, token } = {}) {
  let page;
  const isCached = cache.has(id);
  const targetUrl = getApiUri(`${id}.json${token ? `?token=${token}` : ""}`);

  // Use cached page if present in the store, except when revalidating
  if (!revalidate && isCached) {
    if (import.meta.env.DEV) {
      console.log(`[getPage] Pulling ${id} page data from cache.`);
    }

    return cache.get(id);
  }

  // Otherwise retrieve page data for the first time
  if (import.meta.env.DEV) {
    console.log(
      `[getPage] ${
        revalidate ? `Revalidating ${id} page data.` : `Fetching ${targetUrl}…`
      }`
    );
  }

  try {
    page = await $fetch(targetUrl);
  } catch (error) {
    console.error(error);
    return false;
  }

  if (import.meta.env.DEV && !revalidate) {
    console.log(`[getPage] Fetched ${id} page data`, page);
  }

  // Add page data to the store, respectively overwrite it
  if (!isCached || revalidate) {
    cache.set(id, page);
  }

  return page;
}

/**
 * Checks if a page has been cached already
 *
 * @param {string} id The page id to look up
 * @returns {boolean} `true` if the page exists
 */
function hasPage(id) {
  return cache.has(id);
}

/**
 * Composable to fetch data from the Kirby backend
 */
export function useKirbyApi() {
  return {
    cache,
    hasPage,
    getPage,
  };
}

import { $fetch } from "ohmyfetch";
import { withQuery } from "ufo";
import { useLanguages } from "./";

const cache = new Map();

/**
 * Builds the full API URL for a specific path for the current language
 *
 * @param {string} path The target path
 * @returns {string} The final URL
 */
function getApiUrl(path) {
  const { isMultilang, languageCode } = useLanguages();
  let result = "";

  // Add language path in multi-language setup
  if (isMultilang) {
    result += `/${languageCode}`;
  }

  // Add the API path
  result += `/${import.meta.env.VITE_BACKEND_API_SLUG}`;

  // Add the file path
  result += `/${path}`;

  return result;
}

/**
 * Retrieves page data by id from either network or store
 *
 * @param {string} id The page to retrieve
 * @param {object} [options] Optional options
 * @param {boolean} [options.revalidate=false] Skip cache look-up and fetch page freshly
 * @param {Record<string, string>} [options.query] Custom query parameters
 * @returns {Promise<Record<string, any>|false>} The page's data or `false` if the fetch request failed
 */
async function getPage(id, { revalidate = false, query = {} } = {}) {
  let page;
  const isCached = hasPage(id, query);
  const targetUrl = getApiUrl(withQuery(`${id}.json`, query));

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
 * @param {Record<string, string>} [query] Custom query parameters (optional)
 * @returns {boolean} `true` if the page exists
 */
function hasPage(id, query = {}) {
  return cache.has(withQuery(id, query));
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

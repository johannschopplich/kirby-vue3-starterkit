/**
 * Indicates a multi-language setup
 */
const isMultilang = import.meta.env.VITE_MULTILANG === "true";

/**
 * Current language code
 */
const languageCode = document.documentElement.lang;

if (import.meta.env.DEV && isMultilang) {
  console.log("[useLanguages] Current language code:", languageCode);
}

/**
 * Composable to work with multi-language setups
 */
export function useLanguages() {
  return {
    isMultilang,
    languageCode,
  };
}

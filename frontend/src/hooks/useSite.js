import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * Return the global `site` object
 *
 * @returns {object}
 */
export const useSite = () => kirbyApiStore.getSite()

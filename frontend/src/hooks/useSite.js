import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * Hook for the global `site` object
 *
 * @returns {Object}
 */
export const useSite = () => kirbyApiStore.getSite()

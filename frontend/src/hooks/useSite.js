import { kirbyStore } from '../store/kirbyStore'

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => kirbyStore.getSite()

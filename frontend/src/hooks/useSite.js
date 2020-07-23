import { apiStore } from '../store/apiStore'

/**
 * Hook for the global `site` object
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => apiStore.getSite()

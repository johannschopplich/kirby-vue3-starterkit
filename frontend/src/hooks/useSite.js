import { apiStore } from '../store/apiStore'

/**
 * Hook containing the global `site`
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => apiStore.getSite()

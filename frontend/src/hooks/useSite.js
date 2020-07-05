import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * Hook for global `site`
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => kirbyApiStore.getSite()

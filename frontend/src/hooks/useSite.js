import { kirbyApiStore } from '../store/kirbyApiStore'

/**
 * Hook containing the global `site`
 *
 * @returns {object} Readonly `site` state
 */
export const useSite = () => kirbyApiStore.getSite()

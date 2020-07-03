import { kirbyApiStore } from '../store/modules/kirby-api'

const apiUrl = process.env.NODE_ENV === 'development'
  ? import.meta.env.KIRBY_API_URL
  : window.location.origin

const getPage = async id => {
  const storedPage = kirbyApiStore.getPage(id)

  // Use cached page if already fetched once
  if (storedPage) {
    if (process.env.NODE_ENV === 'development') {
      console.log(`[KirbyAPI] Use cached ${apiUrl}/${id}.json`)
    }

    return storedPage
  }

  // Otherwise fetch page for the first time
  const resp = await fetch(`${apiUrl}/${id}.json`)
  const page = await resp.json()

  if (process.env.NODE_ENV === 'development') {
    console.log(`[KirbyAPI] Fetch ${apiUrl}/${id}.json`)
    console.log(page)
  }

  // Add page data to api store
  kirbyApiStore.addPage({ id, data: page })

  return page
}

export const useKirbyAPI = () => {
  return {
    getPage
  }
}

/**
 * Wait for e.g. transitions
 *
 * @see https://github.com/vuejs/vue-router-next/blob/master/e2e/scroll-behavior/index.ts
 * @returns {object} Scroll waiter functions
 */
function createScrollWaiter () {
  let resolve
  let promise

  /**
   *
   */
  function add () {
    // eslint-disable-next-line promise/param-names
    promise = new Promise(r => {
      resolve = r
    })
  }

  /**
   *
   */
  function flush () {
    resolve && resolve()
    resolve = undefined
    promise = undefined
  }

  const waiter = {
    promise,
    add,
    flush
  }

  Object.defineProperty(waiter, 'promise', {
    get: () => promise
  })

  return waiter
}

export const scrollWaiter = createScrollWaiter()

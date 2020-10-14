/**
 * Log information to console in development environment
 *
 * @param {...*} args Arguments to pass to `console.log`
 */
export default (...args) => {
  if (import.meta.env.DEV) {
    console.log(...args)
  }
}

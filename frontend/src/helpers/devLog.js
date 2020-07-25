/**
 * Log information to console in development environment
 *
 * @param {...*} args Arguments to pass to `console.log`
 */
export default (...args) => {
  if (process.env.NODE_ENV === 'development') {
    console.log(...args)
  }
}

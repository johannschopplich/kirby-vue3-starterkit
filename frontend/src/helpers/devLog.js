/**
 * Log message to console if in development environment
 *
 * @param {Array} args Arguments to pass to `console.log`
 */
export default (...args) => {
  if (process.env.NODE_ENV === 'development') {
    console.log(...args)
  }
}

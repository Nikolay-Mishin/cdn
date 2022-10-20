export const promiseAllArgs = async (...promises) => Promise.all(promises.map(async (p) => await (typeof p == 'function' ? p() : p.shift()(...p))))
export const promiseAll = async (...promises) => Promise.all(promises.map(async (p) => await p()))
export const promiseModules = async (...modules) => Promise.all(modules.map(async (m) => await import(m)))
export const promiseFetches = async (...fetches) => Promise.all(fetches.map(async (f) => await fetch(f)))

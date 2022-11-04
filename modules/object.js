export const assign = (target, obj) => {
    Object.keys(target).forEach((k) => {
        target[k] = obj.hasOwnProperty(k) ? obj[k] : target[k]
    })
    return target
}

export const descriptor = {
	enumerable: false,
	configurable: false,
	writable: false
}

export const setProperty = (obj, key, value, opts = descriptor) => {
	return Object.defineProperty(obj, key, Object.assign(descriptor, opts, { value }))
}

export const filter = (obj, cb = ([k, v]) => true) => Object.fromEntries(Object.entries(obj).filter(cb))
export const map = (obj, cb = ([k, v]) => [k, v]) => Object.fromEntries(Object.entries(obj).map(cb))

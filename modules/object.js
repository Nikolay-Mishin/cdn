export const assign = (target, obj) => {
    Object.keys(target).forEach((k) => {
        target[k] = obj.hasOwnProperty(k) ? obj[k] : target[k]
    })
    return target
}

const descriptor = {
	enumerable: false,
	configurable: false,
	writable: false
}

export const setProperty = (obj, key, opts = descriptor) => {
	return Object.defineProperty(obj, key, Object.assign(descriptor, opts))
}

import { funcName } from './function.js';
import { descriptor, filter, setProperty } from './object.js'

const _descriptor = Object.assign(descriptor, { configurable: true })

export { funcName, _descriptor as descriptor }

export const getPrototype = (obj = Object) => obj.prototype ?? obj.__proto__,
	nullProto = {}.__proto__,
	objProto = Object.prototype,
	arrProto = Array.prototype,
	error = msg => { throw new Error(msg) },
	{ assign, keys, values, fromEntries, entries, getPrototypeOf, getOwnPropertyNames, is: equal } = Object,
	{ isArray, from } = Array,
	is = (context, obj) => (function (obj) { return obj != null && obj.constructor === this; }).call(context, obj),
	isObject = obj => is(Object, obj),
	isFunc = obj => is(Function, obj)

export const register = (() => {
	function _register(obj, value, opts = _descriptor) {
		const key = opts.key || funcName(value)
		const func = { [key]: function () { return value(this, ...arguments); } }
		console.log(func)
		console.log(func[key])
		setProperty(getPrototype(obj), key, func[key], Object.assign(_descriptor, opts))
		return value
	}
	setProperty(nullProto, 'register', function register() { return _register(this, ...arguments); }, _descriptor)
	return _register
})(),
	registerAll = (obj, ...funcs) => funcs.reduce((o, func) => {
		const key = funcName(func)
		return { [key]: obj.register(func) }
	}, {})

export const { filter: filterObj } = registerAll({},
	filter, keys, values
)

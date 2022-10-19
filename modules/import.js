import { config } from '../config/config.js'
import { concat } from './array.js'
import { isObj as $isObj } from './type.js'
import { fileName } from './FS.js'
import { promiseModules } from './promise.js'

const { modules: $modules } = config
const root = $modules['@root']
delete $modules['@root']

const _import = async (...modules) => promiseModules(...modules)

// flatten arguments to enable both - imports([m1, m2]) or imports(m1, m2)
export const imports = (...modules) => _import(concat(modules))

export const $import = async (...modules) => {
	modules = concat(modules)
	modules = modules.length > 0 ? modules : [$modules]

	const checkModules = (modules) => {
		const isObj = $isObj(modules),
			[keys, values] = isObj ? [Object.keys(modules), Object.values(modules)] : [[], []]
		return [isObj, keys, values]
	}

	const [isObj, keys, values] = checkModules(modules[0])
	modules = isObj ? values : modules

	console.log(keys)
	console.log(modules)

	const parseModules = (m, i, modules) => {
		console.log(modules)
		return !$isObj(m) ? m : ((key) => {
			console.log(key)
			console.log(m)

			let [isObj, _keys, values] = checkModules(m)

			console.log(isObj)
			console.log(_keys)
			console.log(values)

			_keys = _keys.map((k) => `${key}.${k}`)

			console.log(_keys)

			keys.splice(i, 1, ..._keys)

			console.log(keys)

			modules.splice(i, 1, ...values)
			return modules.map(parseModules)
		})(keys[i])
	}

	modules.map(parseModules)

	console.log(keys)
	console.log(modules)

	const parseKey = (k) => {
		console.log(k)

		const split = k.split('.')

		console.log(split)

		split.pop()
		const join = split.join('/')

		console.log(join)

		return join ? `${join}/` : join
	}

	keys.forEach((k) => parseKey(k))

	modules = modules.map((m, i) => `${root}${parseKey(keys[i])}${m}`)

	console.log(modules)

	return Object.fromEntries((await _import(...modules)).map((m, i) => [isObj ? keys[i] : fileName(modules[i], false), m]))
}

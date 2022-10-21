import { log } from './dom.js'
import { fileExt } from './FS.js'
import { getData } from './ajax.js'

const blobOpts = {
	type: 'plain',
	data: '',
	url: ''
}

const prefixList = {
	application: ['json'],
	text: ['plain', 'html'],
	image: ['gif', 'png', 'jpeg']
}

export let createBlob = async (opts = blobOpts) => {
	const setType = (path) => {
		const ext = fileExt(path)
		type = ext ? ext : type
		console.log(type)
	}

	opts = Object.assign(blobOpts, opts)
	let { type, data, url } = opts
	type = type.replace('jpg', 'jpeg')

	console.log({ type, data, url })
	console.log(data instanceof Blob)

	if (url) {
		setType(url)
		return await getData(url, 'blob')
	}

	if (data instanceof Blob) return data

	// сериализуем данные
	if (type == 'json') data = JSON.stringify(data)

	const entries = Object.entries(prefixList).filter(([prefix, types]) => types.includes(type))[0]
	const prefix = entries[0]

	console.log(entries)
	console.log(prefix)

	type = `${prefix}/${type}`

	console.log({ type, data, url })

	// создаем файл
	const blob = new Blob([data], { type })
	// проверяем
	console.log('blob: blob')
	console.log(blob)

	return blob
}

import { getData } from './ajax.js'
import { log } from './dom.js'
import { fileExt } from './FS.js'

const blobOpts = {
	type: 'plain',
	data: '',
	file: ''
}

const prefixList = {
	application: ['json'],
	text: ['plain', 'html'],
	img: ['gif', 'png', 'jpg', 'jpeg']
}

export let createBlob = async (opts = blobOpts) => {
	opts = Object.assign(blobOpts, opts)
	let { type, data, url } = opts
	console.log({ type, data, url })
	if (url) {
		const ext = fileExt(url)
		type = ext ? ext : type
		console.log(type)
		const result = getData(url)
		console.log(result)
	}
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
	console.log(blob)
	return blob
}

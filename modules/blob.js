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
	let { type, data, url, file } = opts
	console.log({ type, data, url, file })
	if (url) {
		console.log(fileExt(url))
		//response = await fetch(url)
		//console.log(await response.json())
	}
	if (file) type = fileExt(file)
	// сериализуем данные
	if (type == 'json') data = JSON.stringify(data)
	const entries = Object.entries(prefixList).filter(([prefix, types]) => types.includes(type))[0]
	const prefix = entries[0]
	console.log(entries)
	console.log(prefix)
	type = `${prefix}/${type}`
	console.log({ type, data, url, file })
	// создаем файл
	const blob = new Blob([data], { type })
	// проверяем
	console.log(blob)
	return blob
}

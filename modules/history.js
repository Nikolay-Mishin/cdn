import { getById } from "./dom.js"

const setContent = (data) => {
	data = data || {}
	data.update = data.update || false
	data.title = data.title || document.title
	data.id = data.id || 'content'
	data.html = data.html || ''
	const { title, id, html } = data
	if (title) document.title = title
	if (id) getById(id).innerHTML = html
	return data
}

const init = (data, cb) => {
	data = setContent(data)
	cb(data)
	console.log(data)
	return data
}

export const setState = (url, data, cb = () => { }) => {
	const { update, title } = init(data, cb)
	history[!update ? 'pushState' : 'replaceState'](data, title, url)
}

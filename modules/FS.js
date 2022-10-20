import { log } from './dom.js'

export const trimExt = (path) => path.replace(/\.[^/.]+$/, '')

export const fileName = (path, ext = true) => {
	const name = path.split('/').pop()
	return ext ? name : trimExt(name)
}

export const read = (file, cb, readAs = 'text') => {
	let reader = new FileReader();

	switch (readAs) {
		case "text":
			readAs = 'readAsText'
			break;
		case "url":
			readAs = 'readAsDataURL'
			break;
		case "buffer":
			readAs = 'readAsArrayBuffer'
			break;
		default:
			readAs = 'readAsText'
	}

	console.log(readAs)

	reader[readAs](file)
	reader.onload = () => cb(reader.result)
	reader.onerror = () => console.log(reader.error)

	return;
}

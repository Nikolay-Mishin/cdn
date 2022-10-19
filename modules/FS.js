import { log } from './dom.js'

export const trimExt = (path) => path.replace(/\.[^/.]+$/, '')

export const fileName = (path, ext = true) => {
	const name = path.split('/').pop()
	return ext ? name : trimExt(name)
}

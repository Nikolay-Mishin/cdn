import { log } from './dom.js'

export const jsonParse = (item) => {
	try { item = JSON.parse(item) }
	catch (e) { return null }
	return item
}

import { get, getAll } from '../../modules/dom.js'
import { observeDOM } from '../../modules/observeDOM.js'

//await import('https://cdn/api/testit/api.js')

const treeQuery = '.list-wrapper'
const titleQuery = '.list-item__title'

//(await import('https://cdn/api/testit/api.js')).init()
export const init = async () => {
	const treeWrapper = get(treeQuery)
	const titleList = getAll(titleQuery)

	console.log(treeWrapper)
	console.log(titleList)

	titleList.forEach((title) => console.log(title.innerText))

	const observer = observeDOM(treeWrapper).event('childList', (m, addedNodes) => console.log(m))
}

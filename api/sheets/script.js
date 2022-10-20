import { log, getById, filterNodes } from '../../modules/dom.js'
import { observeDOM } from '../../modules/observeDOM.js'
import { handleAuthClick, handleSignoutClick } from './api.js'
import { getData, getRange, setData, updateData } from './modules/data.js'

const authorize_button = getById('authorize_button')
observeDOM(authorize_button, { attributes: true }).event('childList', (m, addedNodes) => {
	let gapiLoaded = false
	addedNodes.forEach((node) => { if (node.data === 'Authorize') gapiLoaded = true })
	console.log(`gapiLoaded: ${gapiLoaded}`)
	if (!gapiLoaded) return
	const cbList = [getData, getData, setData, updateData, handleSignoutClick]
	const buttons = filterNodes(getById('buttonsAPI').childNodes, 'button')
	buttons.forEach((btn, i) => btn.addEventListener('click', () => handleAuthClick(cbList[i])))
})

const range = getRange('Test', '', 'B2')
console.log(range)

console.log(filterNodes(getById('buttonsAPI').childNodes, 'button'))

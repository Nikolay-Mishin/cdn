import { log, getById } from './dom.js'
import { observeDOM } from '../../modules/observeDOM.js'

export function observeDemo() {
	// Выбираем целевой элемент
	let target = getById('observeDemo')

	// add item
	let itemHTML = "<li><button>list item (click to delete)</button></li>",
		listElm = target.querySelector('ol')

	let mutations, addedNodes, removedNodes

	// Колбэк-функция при срабатывании мутации
	const cb = (m, $mutations, $addedNodes, $removedNodes) => {
		mutations = mutations || $mutations
		addedNodes = addedNodes || $addedNodes
		removedNodes = removedNodes || $removedNodes
	}

	const observer = observeDOM(target, cb)

	console.log(observer)

	target.querySelector('button').addEventListener('click', (e) => {
		listElm.insertAdjacentHTML("beforeend", itemHTML)
	})

	// delete item
	listElm.addEventListener('click', (e) => {
		if (e.target.nodeName == "BUTTON")
			e.target.parentNode.parentNode.removeChild(e.target.parentNode)
	})

	// Insert 3 DOM nodes at once after 3 seconds
	setTimeout(() => {
		listElm.removeChild(listElm.lastElementChild)
		listElm.insertAdjacentHTML("beforeend", Array(4).join(itemHTML))
	}, 3000)

	// Позже можно остановить наблюдение
	//observer.disconnect()
}

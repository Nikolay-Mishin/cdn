import { config } from '../config/config.js'
import { push } from './array.js'
import { setProperty } from './object.js'

const { observer: configObserver } = config.core

// Конфигурация observer (за какими изменениями наблюдать)
const configObserverDef = {
	// true, если необходимо наблюдать за добавлением или удалением дочерних элементов (Включая текстовые узлы (text nodes))
	childList: true,
	// true, если необходимо наблюдать за потомками целевого элемента.
	subtree: true,
	// true, если необходимо наблюдать за изменениями атрибутов целевого элемента.
	attributes: true,
	// true, если необходимо наблюдать за изменениями значения текстового содержимого целевого узла node.data (текстовых узлов дочернего элемента).
	characterData: true,
	// true, если необходимо возвращать предыдущее значение атрибута (также требуется опция characterData).
	attributeOldValue: true,
	// true, если необходимо возвращать предыдущее значение Data атрибута (также требуется опция attributes).
	characterDataOldValue: true,
	// Устанавливает массив названий атрибутов (без указания пространства имён), если требуется наблюдать за изменениями конкретных атрибутов.
	//attributeFilter: []
}

// Затем, после изменений, выполняется callback, в который изменения передаются первым аргументом как список объектов MutationRecord, а сам наблюдатель идёт вторым аргументом.
// Объекты MutationRecord имеют следующие свойства:
//* type – тип изменения, один из:
//	- "attributes" изменён атрибут,
//	- "characterData" изменены данные elem.data, это для текстовых узлов
//	- "childList" добавлены / удалены дочерние элементы,
//* target – где произошло изменение: элемент для "attributes", текстовый узел для "characterData" или элемент для "childList",
//* addedNodes / removedNodes – добавленные / удалённые узлы,
//* previousSibling / nextSibling – предыдущий или следующий одноуровневый элемент для добавленных / удалённых элементов,
//* attributeName / attributeNamespace – имя / пространство имён(для XML) изменённого атрибута,
//* oldValue – предыдущее значение, только для изменений атрибута или текста, если включена соответствующая опция attributeOldValue / characterDataOldValue.
export const observeDOM = (obj, opts = configObserver) => {
	const MutationObserver = window.MutationObserver || window.WebKitMutationObserver

	if (!obj || obj.nodeType !== 1) return

	if (MutationObserver) {
		let observer

		// Колбэк-функция при срабатывании мутации
		const _cb = (mutations) => {
			const { events } = observer
			const { childList, attributes, characterData, cb, changed } = events

			//console.log(events)

			let addedNodes = [], removedNodes = []

			mutations.forEach(m => {
				//console.log(m)

				push(addedNodes, m.addedNodes)
				push(removedNodes, m.removedNodes)

				if (m.type == 'childList') {
					//console.log(`A child node has been ${m.addedNodes.length ? 'added' : 'removed'}.`)
					childList(m, addedNodes, removedNodes)
				}
				else if (m.type == 'attributes') {
					//console.log('The ' + m.attributeName + ' attribute was modified.')
					attributes(m)
				}
				else if (m.type == 'characterData') {
					//console.log('The characterData was modified.')
					characterData(m, addedNodes, removedNodes)
				}

				cb(m, addedNodes, removedNodes)
			})

			//console.log(observer)

			//console.log(mutations)

			//console.log('Added:')
			//console.log(addedNodes)
			//console.log('Removed:')
			//console.log(removedNodes)

			changed(mutations, addedNodes, removedNodes)
		}

		// Создаём экземпляр наблюдателя с указанной функцией колбэка
		observer = new MutationObserver(_cb)
		// Начинаем наблюдение за настроенными изменениями целевого элемента
		observer.observe(obj, Object.assign(configObserver, opts))

		setProperty(observer, 'events', {
			value: {
				changed: () => console.log('Observer event: changed'),
				cb: () => { 'Observer: iterate' },
				childList: () => { },
				attributes: () => { },
				characterData: () => { }
			}
		})

		setProperty(observer, 'event', {
			value: (ev, cb = () => { }) => {
				if (observer.events.hasOwnProperty(ev)) observer.events[ev] = cb
				console.log(observer)
				return observer
			}
		})

		return observer
	}
	// browser support fallback
	else if (window.addEventListener) {
		const events = ['DOMNodeInserted', 'DOMNodeRemoved', 'DOMSubtreeModified', 'DOMAttrModified', 'DOMCharacterDataModified', 'DOMAttributeNameChanged', 'DOMElementNameChanged', 'DOMNodeInsertedIntoDocument', 'DOMNodeRemovedFromDocument']
		events.forEach((e) => obj.addEventListener(e, cb, false))
	}
}

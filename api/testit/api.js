import { config } from '../../config/config.js'
import { ajax, getData as $getData } from '../../modules/ajax.js'
import { push } from '../../modules/array.js'
import { addEvent, get, getByClass, getById } from '../../modules/dom.js'
import { observeDOM } from '../../modules/observeDOM.js'
import { capitalizeFirstLetter } from '../../modules/str.js'
//import { descriptor } from '../../modules/prototype.js'

//console.log(descriptor)

//await import('https://cdn/api/testit/api.js')

const { host_local } = config
const { root } = config.path.data
const { root: testit, treeFile, tableFile, tcFile } = config.path.data.api.testit
const { root: rootServer, saveFile } = config.path.server
const saveFilePath = `${rootServer}/${saveFile}`

const fileTree = `${root}/${testit}/${treeFile}`
const fileTreeData = `${host_local}/${fileTree}`
const fileTable = `${root}/${testit}/${tableFile}`
const fileTableData = `${host_local}/${fileTable}`
const fileTC = `${root}/${testit}/${tcFile}`
const fileTCData = `${host_local}/${fileTC}`

console.log(fileTreeData)
console.log(fileTableData)
console.log(fileTCData)

const treeQuery = '.list-wrapper'
const titleClass = 'list-item__title'
const titleWrapClassName = 'section-tree__row ng-star-inserted'
const viewportQuery = '.ag-body-viewport'
const cellClass = 'ag-cell-value'
const rowClass = 'ag-row'
const catClass = 'ag-full-width-row'
const modalWrapId = 'overlay-container'
const modalQuery = '.modal-content'

const viewport = get(viewportQuery)
const modalWrap = getById(modalWrapId)

console.log(viewport)
console.log(modalWrap)

const getTitles = (list) => list.forEach((item) => console.log(item))
const setTitles = (list) => [...list].map((item) => item.innerText)
const getTable = (schema, list) => list.map((item) => {
	const obj = Object.fromEntries(Object.entries(schema).map(([k, v], i) => [k, item[i]]))
	return obj
})
const getModal = () => get(modalQuery, modalWrap)
const save = async (file, data) => await ajax(saveFilePath, { file, data })

//(await import('https://cdn/api/testit/api.js')).getData()
export const getData = async (type = 'table') => {
	return await $getData(eval(`file${capitalizeFirstLetter(type)}Data`))
}

//(await import('https://cdn/api/testit/api.js')).getInvalidData()
export const getInvalidData = async (type = 'table') => {
	const { data } = await getData(type)
	console.log(data)
	if (type !== 'table') return
	const invalidData = data.filter((v) => Object.keys(v).length < 6)
	console.log(invalidData)
}

//(await import('https://cdn/api/testit/api.js')).saveTC()
export const saveTC = async () => {
	let changed = false
	observeDOM(modalWrap).event('childList', (m, addedNodes) => {
		//console.log(m)
		changed = false
		if (addedNodes.length && getModal() && m.target.classList.contains('ellipsis')) {
			//console.log(addedNodes)
			//console.log(getModal())
			//console.log(m.target)
			changed = true
		}
	}).event('changed', async (mutations, addedNodes) => {
		if (changed) {
			//console.log(mutations)
			//console.log(addedNodes)
			changed = false
			const modal = getModal()
			const id = get('.title', modal).innerText.replace('#', '')
			//console.log(id)
			//console.log(`${fileTC.replace('tc.', `tc-${id}.`)}`)
			await save(`${fileTC.replace('tc.', `tc-${id}.`)}`, modal.outerHTML)
		}
	})
}

//(await import('https://cdn/api/testit/api.js')).saveTree()
export const saveTree = async () => {
	const tree = get(treeQuery)
	const titleList = setTitles(getByClass(titleClass))

	//console.log(tree)
	console.log(titleList)

	await save(fileTree, titleList)

	observeDOM(tree).event('changed', async (mutations, addedNodes) => {
		const filtered = addedNodes.filter((node) => node.className == titleWrapClassName).map((node) => node.innerText)
		//console.log(mutations)
		if (addedNodes.length) console.log(addedNodes)
		//console.log(filtered)

		push(titleList, filtered)

		console.log(titleList)

		await save(fileTree, titleList)
	})
}

//(await import('https://cdn/api/testit/api.js')).saveTable()
export const saveTable = async () => {
	const itemList = setTitles(getByClass(cellClass)).filter((node) => node.length)
	const tableSchema = { id: '', title: '', priority: '', status: '', date: '', author: '' }

	//console.log(viewport)
	console.log(itemList)

	let tempList = []
	const tableContent = getTable(tableSchema, itemList.reduce((list, item) => {
		tempList.push(item)
		if (tempList.length == 6) {
			list.push(tempList)
			tempList = []
		}
		return list
	}, []))

	console.log(tableContent)

	await save(fileTable, tableContent)

	observeDOM(viewport).event('changed', async (mutations, addedNodes) => {
		//console.log(mutations)
		if (addedNodes.length) {
			const isCat = (node) => node.classList?.contains(catClass)
			const verify = (node) => node.classList.contains(rowClass) && node.innerText && !isCat(node)
			const cat = addedNodes.filter((node) => isCat(node)).shift() || ''
			const filtered = addedNodes.filter((node) => verify(node))
				.map((node) => node.innerText).map((str) => str.split('\n'))
			//console.log(addedNodes)
			//console.log(cat)
			//console.log(filtered)

			const tableContentChanged = getTable(tableSchema, filtered)
			push(tableContent, tableContentChanged)

			//console.log(tableContentChanged)
			console.log(tableContent)

			await save(fileTable, tableContent)
		}
	})
}

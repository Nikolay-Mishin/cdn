import { config } from '../../config/config.js'
import { ajax, getData as $getData } from '../../modules/ajax.js'
import { push } from '../../modules/array.js'
import { get, getByClass } from '../../modules/dom.js'
import { observeDOM } from '../../modules/observeDOM.js'
//import { descriptor } from '../../modules/prototype.js'

//console.log(descriptor)

//await import('https://cdn/api/testit/api.js')

const { root } = config.path.data
const { root: testit, treeFile, tableFile } = config.path.data.api.testit
const { root: rootServer, saveFile } = config.path.server
const saveFilePath = `${rootServer}/${saveFile}`
const file = `${root}/${testit}/${tableFile}`
const fileData = `https://cdn/${file}`
const fileTree = `${root}/${testit}/${tableFile}`
const fileTreeData = `https://cdn/${file}`

console.log(file)
console.log(fileData)

const treeQuery = '.list-wrapper'
const titleClass = 'list-item__title'
const titleWrapClassName = 'section-tree__row ng-star-inserted'
const viewportQuery = '.ag-body-viewport'
const cellClass = 'ag-cell-value'
const rowClass = 'ag-row'
const catClass = 'ag-full-width-row'

//(await import('https://cdn/api/testit/api.js')).getData()
export const getData = async () => {
	return await $getData(fileData)
}

//(await import('https://cdn/api/testit/api.js')).getInvalidData()
export const getInvalidData = async () => {
	const { data } = await getData(fileData)
	console.log(data)
	const invalidData = data.filter((v) => Object.keys(v).length < 6)
	console.log(invalidData)
}

//(await import('https://cdn/api/testit/api.js')).init()
export const init = async () => {
	const getTitles = (list) => list.forEach((item) => console.log(item))
	const setTitles = (list) => [...list].map((item) => item.innerText)
	const getTable = (schema, list) => list.map((item) => {
		const obj = Object.fromEntries(Object.entries(schema).map(([k, v], i) => [k, item[i]]))
		return obj
	})

	const tree = get(treeQuery)
	const viewport = get(viewportQuery)
	const titleList = setTitles(getByClass(titleClass))
	const itemList = setTitles(getByClass(cellClass)).filter((node) => node.length)
	const tableSchema = { id: '', title: '', priority: '', status: '', date: '', author: '' }

	//console.log(tree)
	//console.log(viewport)
	console.log(titleList)
	console.log(itemList)

	//getTitles(titleList)
	//getTitles(itemList)

	let tempList = []
	const tableContent = getTable(tableSchema, itemList.reduce((list, item) => {
		tempList.push(item)
		if (tempList.length == 6) {
			list.push(tempList)
			tempList = []
		}
		return list
	}, []))

	//console.log(tableContent)

	observeDOM(tree).event('changed', (mutations, addedNodes) => {
		const filtered = addedNodes.filter((node) => node.className == titleWrapClassName).map((node) => node.innerText)
		//console.log(mutations)
		if (addedNodes.length) console.log(addedNodes)
		//console.log(filtered)

		push(titleList, filtered)

		console.log(titleList)
	})

	observeDOM(viewport).event('changed', (mutations, addedNodes) => {
		//console.log(mutations)
		if (addedNodes.length) {
			const isCat = (node) => node.classList.contains(catClass)
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
			ajax(saveFilePath, { file, data: tableContent })
		}
	})
}

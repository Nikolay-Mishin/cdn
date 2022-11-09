import { config } from '../../config/config.js'
import { ajax, getData as $getData } from '../../modules/ajax.js'
import { push } from '../../modules/array.js'
import { addEvent, createEl, get, getAll, getByClass, getById } from '../../modules/dom.js'
import { click } from '../../modules/events.js'
import { fileExt, fileName } from '../../modules/FS.js'
import { getImgData, imgToBlob } from '../../modules/img.js'
import { observeDOM } from '../../modules/observeDOM.js'
import { capitalizeFirstLetter } from '../../modules/str.js'
//import { descriptor } from '../../modules/prototype.js'

//console.log(descriptor)

//await import('https://cdn/api/testit/api.js')

const host = 'https://testit.infologistics.ru'

const { host_local } = config
const { root } = config.path.data
const { root: testit, treeFile, tableFile, catFile, tableInvalidFile, treeCatFile, treeCatInvalidFile, tcFile, imgTC } = config.path.data.api.testit
const { root: rootServer, saveFile } = config.path.server
const saveFilePath = `${rootServer}/${saveFile}`

const fileTree = `${root}/${testit}/${treeFile}`
const fileTreeData = `${host_local}/${fileTree}`
const fileTable = `${root}/${testit}/${tableFile}`
const fileTableData = `${host_local}/${fileTable}`
const fileCat = `${root}/${testit}/${catFile}`
const fileCatData = `${host_local}/${fileCat}`
const fileTableInvalid = `${root}/${testit}/${tableInvalidFile}`
const fileTableInvalidData = `${host_local}/${fileTableInvalid}`
const fileTreeCat = `${root}/${testit}/${treeCatFile}`
const fileTreeCatData = `${host_local}/${fileTreeCat}`

const fileTC = `${root}/${testit}/${tcFile}`
const fileTCData = `${host_local}/${fileTC}`
const imgTCPath = `${root}/${testit}/${imgTC}`

console.log(fileTreeData)
console.log(fileTableData)
console.log(fileCatData)
console.log(fileTableInvalidData)
console.log(fileTreeCatData)

console.log(fileTCData)
console.log(imgTCPath)

const tableSchema = { id: '', title: '', priority: '', status: '', date: '', author: '', tag: '', catId: 0, cat: '', type: '' }

const treeQuery = '.list-wrapper'
const titleClass = 'list-item__title'
const titleWrapClassName = 'section-tree__row ng-star-inserted'
const viewportQuery = '.ag-body-viewport'
const cellClass = 'ag-cell-value'
const rowClass = 'ag-row'
const catClass = 'ag-full-width-row'
const catQuery = '.section-data__name'
const modalWrapId = 'overlay-container'
const modalQuery = '.modal-content'
const idQuery = '.title'
const fileQuery = 'img'

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
const getPostfix = (path, id, replace = '.json') => path.replace(replace, `-${id + replace}`)
const save = async (file, data, action) => await ajax(saveFilePath, { file, data, action })

//(await import('https://cdn/api/testit/api.js')).getData()
export const getData = async (type = 'table', postfix = '') => {
	let path = eval(`file${capitalizeFirstLetter(type)}Data`)
	path = postfix ? getPostfix(path, postfix) : path
	console.log(path)
	const { data } = await $getData(path)
	console.log(data)
	return data
}

//(await import('https://cdn/api/testit/api.js')).getInvalidData()
export const getInvalidData = async () => {
	const data = await getData()
	const invalidData = data.filter((v) => Object.keys(v).length < Object.keys(tableSchema).length)
	console.log(invalidData)
	return invalidData
}

//(await import('https://cdn/api/testit/api.js')).getTCData(1940)
export const getTCData = async (id) => {
	getData('TC', id)
}

//(await import('https://cdn/api/testit/api.js')).editFields(['tag', [1]], ['type', '<img src="../img/icon/test_cases.png" alt="note" style="height:18px; width: 16px;"><img src="../img/icon/no-autotests.png" alt="note" style="height:18px; width: 16px;">'])
export const editFields = async (...fields) => {
	const data = await getData()
	data.forEach((item) => fields.forEach(([k, v]) => item[k] = v))
	console.log(data)
	await save(fileTable, data)
}

//(await import('https://cdn/api/testit/api.js')).saveInvalidData()
export const saveInvalidData = async () => {
	const invalidData = await getInvalidData()
	await save(fileTableInvalid, invalidData)
}

//(await import('https://cdn/api/testit/api.js')).saveTreeData()
export const saveTreeData = async () => {
	const table = await getData()
	const catList = await getData('cat')
	const countTable = table.length
	let count = 0

	const data = catList.map((_cat) => {
		const obj = {
			cat: _cat,
			childList: table.filter(({ cat }) => cat == _cat)
		}
		const countChild = obj.childList.length
		count += countChild
		obj.count = countChild
		return obj
	})

	console.log(countTable)
	console.log(count)
	console.log(data)

	await save(fileTreeCat, data)
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
		console.log(changed)
		if (changed) {
			//console.log(mutations)
			//console.log(addedNodes)

			changed = false

			const getImgPath = (id, i) => `${imgTCPath}/${id}/${i}.png`

			const modal = getModal()
			let html = modal.outerHTML
			const id = get(idQuery, modal).innerText.replace('#', '')

			console.log(modal)
			console.log(id)
			console.log(getPostfix(fileTC, id))

			const files = [...getAll(fileQuery, modal)]

			console.log(files)

			files.forEach(async (img, i) => {
				const { data } = getImgData(img)
				const src = img.src.replace(host, '').split('?').shift()
				console.log(getImgPath(id, i))
				//console.log(src)
				//console.log(typeof html)
				//console.log(...html.matchAll(src))
				html = html.replace(src, getImgPath(id, i))
				await save(getImgPath(id, i), data, 'saveImg')
			})

			console.log(html)

			await save(getPostfix(fileTC, id), html)
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
	const catList = []
	const nodeList = [...getByClass(cellClass)].filter((node) => node.innerText.length)
	const itemList = setTitles(nodeList)

	//console.log(viewport)
	//console.log(nodeList)
	console.log(itemList)

	let tempList = []
	const getCatName = (node) => get(catQuery, node)
	let getCat = (id = 0, node = null) => getCatName(node || get(`[row-index="${id}"]`)).innerText
	let setCat = (id = 0, cat = getCat(id)) => {
		catList.push(cat)
		//console.log(catList)
		return cat
	}
	let cat = setCat()
	const getCatId = () => catList.length - 1

	const tableContent = getTable(tableSchema, itemList.reduce((list, item, i) => {
		tempList.push(item)
		if (tempList.length == 6) {
			tempList.push('') // tag
			const nodeRow = nodeList[i].closest(`.${rowClass}`)
			let rowIndex = nodeRow.getAttribute('row-index')
			const prevRowIndex = rowIndex - 1
			const prevNode = get(`[row-index="${prevRowIndex}"]`)

			//console.log(nodeList[i])
			//console.log(nodeRow)
			//console.log(rowIndex)
			//console.log(prevRowIndex)
			//console.log(prevNode)
			//console.log(prevNode.classList.contains(catClass))
			//console.log(getCatId())

			if (prevNode.classList.contains(catClass) && prevRowIndex) cat = setCat(prevRowIndex)

			//console.log(cat)
			//console.log(getCatId())

			tempList.push(getCatId())
			tempList.push(cat)

			list.push(tempList)
			tempList = []

			//console.log(list)
		}
		return list
	}, []))

	console.log(catList)
	console.log(tableContent)

	await save(fileTable, tableContent)
	await save(fileCat, catList)

	observeDOM(viewport).event('changed', async (mutations, addedNodes) => {
		//console.log(mutations)
		if (addedNodes.length) {
			const isCat = (node) => node.classList?.contains(catClass)
			const verify = (node) => node.classList.contains(rowClass) && node.innerText && !isCat(node)
			const catNode = addedNodes.filter((node) => isCat(node)).shift() || null
			const filtered = addedNodes.filter((node) => verify(node))
				.map((node) => node.innerText).map((str) => str.split('\n'))

			//console.log(addedNodes)
			//console.log(catNode)

			if (catNode) cat = setCat(0, getCat(0, catNode))

			filtered.map((item) => {
				item.push('') // tag
				item.push(getCatId())
				item.push(cat)
				return item
			})

			console.log(catList)
			//console.log(filtered)

			const tableContentChanged = getTable(tableSchema, filtered)
			push(tableContent, tableContentChanged)

			//console.log(tableContentChanged)
			console.log(tableContent)

			await save(fileTable, tableContent)
			await save(fileCat, catList)
		}
	})
}

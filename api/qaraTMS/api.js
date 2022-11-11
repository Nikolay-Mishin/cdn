import { config } from '../../config/config.js'
import { getData } from '../../modules/ajax.js'
import { concat } from '../../modules/array.js'
import { getById, getByClass, get, getAll, filterNodes } from '../../modules/dom.js'
import { click } from '../../modules/events.js'
import { observeDOM } from '../../modules/observeDOM.js'
import { interval } from '../../modules/time.js'
import { getCountCases } from './modules/getCountCases.js'

/**
 * await import('https://cdn/api/qaraTMS/api.js')
 * (await import('https://cdn/api/qaraTMS/api.js')).init()
 * (await import('https://cdn/api/qaraTMS/api.js')).getSuites()
 */

const repoClass = 'text-muted'

const { root } = config.path.data
const { root: qaraTMS } = config.path.data.api.qaraTMS
const { root: rootServer, saveFile } = config.path.server
const saveFilePath = `${rootServer}/${saveFile}`

const repo = (() => getByClass(repoClass)[0].parentNode.innerText.replace('Repository: ', '').replace(' ', ''))()
const file = `${root}/${qaraTMS}/${repo}.json`

console.log(qaraTMS)
console.log(file)

export const init = async () => {
	await getSuites()
}

export const getSuites = async () => {
	const data = {}

	const saveToFile = async () => {
		console.log(data)
		data.cases = getCountCases(data)
		const result = await getData(saveFilePath, { file, data })
		console.log(result)
		return result
	}

	const idTree = 'tree'
	const idList = 'test_cases_list'

	const titleClass = 'branch-title'
	const tcTitleClass = 'test_case_title'

	const idTc = 'test_case_area'
	const idContent = 'test_case_content'
	const precondClass = 'rounded'
	const idSteps = 'steps_container'

	const treeWrap = getById(idTree)
	const tcWrap = getById(idList)
	const tcContentWrap = getById(idTc)

	console.log(treeWrap)
	console.log(tcWrap)
	console.log(tcContentWrap)

	const getBranchEl = (branch) => getByClass(titleClass, branch)[0]
	const branchNodes = filterNodes(treeWrap.childNodes, 'li')
	const branchList = branchNodes.map((branch) => getBranchEl(branch))
	const suites = branchNodes.length
	data.suites = suites

	console.log(branchList)

	let id, branchEl, branch

	const nextBranch = async () => {
		console.log(branchList)

		if (!branchList.length) return await saveToFile()

		const nextBranchEl = branchList[0]

		console.log(nextBranchEl)

		click(nextBranchEl)

		return
	}

	let childListChanged = false

	// Колбэк-функция при срабатывании мутации
	const branchClick = (m, addedNodes) => {
		childListChanged = false

		console.log(childListChanged)
		console.log(branchList)
		console.log(branchEl)
		console.log(branch)

		console.log(data)

		const tcList = filterNodes(addedNodes, 'div')

		console.log(tcList)

		if (!tcList.length) return

		childListChanged = true

		console.log(childListChanged)

		const getTcInfo = (el) => {
			let tcId = el.id
			let tcTitleEl = getByClass(tcTitleClass, el)[0]
			let tcTitle = get('span', tcTitleEl).innerText
			return { tcId, tcTitleEl, tcTitle }
		}

		let tcEl = tcList[0]
		const { tcId, tcTitleEl, tcTitle } = getTcInfo(tcEl)

		console.log(tcEl)
		console.log({ tcId, tcTitleEl, tcTitle })

		click(tcTitleEl)

		const tcClick = () => {
			const nextTc = () => {
				if (!tcList.length) return nextBranch()

				const { tcTitleEl: nextTc } = getTcInfo(tcList[0])

				console.log(nextTc)

				click(nextTc)

				return
			}

			console.log(tcList)

			tcEl = tcList.shift()

			console.log(tcEl)

			if (!tcEl) return

			const { tcId, tcTitleEl, tcTitle } = getTcInfo(tcEl)

			console.log({ tcId, tcTitleEl, tcTitle })

			const tc = getById(idContent)

			console.log(tc)

			const precondWrap = getByClass(precondClass, tc)[0]
			const stepsWrap = getById(idSteps)

			console.log(precondWrap)
			console.log(stepsWrap)

			if (!stepsWrap) {
				data[id][tcId] = { id: tcId, title: tcTitle }
				return nextTc()
			}

			const precondList = concat(getAll('p', precondWrap))
			const stepsList = filterNodes(stepsWrap.childNodes, 'div')

			stepsList.shift()

			console.log(precondList)
			console.log(stepsList)

			const getContent = (target) => {
				console.log(target)
				console.log(target.innerText)
				const img = get('img', target) || { dataset: {} }
				const { src } = img
				const { filename } = img.dataset
				console.log(filename)
				return src ? { filename, src } : target.innerText.replace('\n', '')
			}

			const preconditions = precondList.map((precond) => {
				const content = getContent(precond)
				console.log(content)
				return content
			})

			const steps = stepsList.map((step) => {
				const cols = concat(getByClass('col-6', step))
				console.log(cols)
				const content = cols.map((col) => {
					const pList = concat(getAll('p', col))
					console.log(pList)
					const content = pList.map((p) => getContent(p))
					console.log(content)
					return content
				})
				return content
			}).map((step) => ({ action: step[0], ER: step[1] }))

			console.log(preconditions)
			console.log(steps)

			data[id][tcId] = { id: tcId, title: tcTitle, preconditions, steps }

			console.log(data)

			console.log(tcList)

			nextTc()
		}

		const observerTc = observeDOM(tcContentWrap).event('childList', tcClick)

		console.log(observerTc)
		console.log(data)
	}

	const observer = observeDOM(tcWrap).event('childList', branchClick).event('changed', (mutations) => {
		mutations.forEach((m) => console.log(m.type))

		console.log(mutations)
		console.log(tcWrap)
		console.log(tcWrap.childNodes)
	})

	const observerTree = observeDOM(treeWrap).event('attributes', (m) => {
		if (m.attributeName != 'class') return

		const { target } = m
		const targetBranchEl = getBranchEl(target)

		console.log(m)
		console.log(target)
		console.log(target.classList)
		console.log(targetBranchEl)
		console.log(branchList)
		console.log(branchList.includes(targetBranchEl))

		if (!branchList.includes(targetBranchEl)) return

		const branchNode = branchNodes.shift()
		id = branchNode.id
		branchEl = branchList.shift()
		branch = branchEl.innerText

		console.log(id)
		console.log(branchNode)
		console.log(branchEl)
		console.log(branch)

		const { level, pid } = branchNode.dataset

		data[id] = { id, branch, level, pid }

		console.log(data)
		console.log(data[id])

		interval(() => {
			console.log(childListChanged)
			if (!childListChanged) nextBranch()
		})
	})

	console.log(observer)
	console.log(observerTree)
	console.log(branchList)

	console.log(data)

	click(branchList[0])
}

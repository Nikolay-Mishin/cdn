import { config } from '../../../config/config.js'
import { getData as ajaxGetData } from '../../../modules/ajax.js'
import { getValues } from './get.js'
import { batchUpdate, updateValues } from './update.js'
import { getCase, createCase } from './case.js'

const { sheets } = config.api
const { spreadsheetId, range } = sheets
const { root, cases } = config.path.data
const { root: serverRoot, saveFile } = config.path.server
const saveFilePath = `${serverRoot}/${saveFile}`
const casesPath = `${root}/${cases}`

export const getData = async (_spreadsheetId = spreadsheetId, _range = range, filter = v => v.length) => {
	console.log(gapi.client.getToken())
	console.log(_spreadsheetId)
	console.log(_range)

	const { response, result, values, numRows } = await getValues(_spreadsheetId, _range)

	const data = values.filter(filter)

	console.log(result)
	console.log(response)
	console.log(numRows)
	console.log(values)
	console.log(data)

	return data
}

export const setData = async (_spreadsheetId = spreadsheetId, _range = range, filter = v => v.length) => {
	const data = await getData(_spreadsheetId, _range, filter)

	const caseList = await createCase(data)

	console.log(caseList)

	const result = await ajaxGetData(saveFilePath, { file: casesPath, data: caseList })

	await getCase()

	return caseList
}

export const updateData = async () => {
	const id = '18VSU4PxNrS1vGEbCrO5O4OvtIBtfHkR-0WW4W5MBs_0'

	//_batchUpdate(id)
	//_updateValues(id)

	const range = getRange('Тест-кейс', 'A2', 'F4')

	const data = await getData(spreadsheetId, range, v => v.length > 1)
	const caseList = (await ajaxGetData(`/${casesPath}`)).data

	console.log(range)
	console.log(data)
	console.log(caseList)

	const head = []

	if (data[0].length < 4) data[0].push('', '')
	else data[0].pop()

	if (data[1].length < 4) data[1].push('', '')
	else data[1].pop()

	console.log(data)

	data.forEach((row) => head.push(row))

	console.log(head)

	const rangeSave = getRange('Test', '', 'F28')
	const values = []

	console.log(rangeSave)

	for (let cat in caseList) {
		const cases = data[cat]
		console.log(cases)
		for (let id in cases) {
			let _head = head
			const _case = cat[id]
			const { title, preconditions, steps } = _case
			_head[0].push(title)
			_head[1].push(preconditions)
			console.log(_head)
			console.log(_case)
			console.log({ title, preconditions, steps })
			Object.values(steps).forEach((step, i) => {
				const { id, desc, data, ER, AR } = step
				values.push(i ? '' : id, desc, data, ER, AR)
			})
		}
		values.push([], [])
	}

	console.log(values)

	//const { result } = await updateValues(id, range, values)

	//console.log(result)
}

export const getRange = (sheet, start = '', end = '') => {
	let range = ''

	if (sheet) range = `'${sheet}'`
	if (sheet && start) range += '!'
	if (!start) start = 'A1'
	range += `${start}`
	if (end) range += `:${end}`

	return range
}

const _batchUpdate = async (id) => {
	const title = "Title"
	const find = "title"
	const replacement = "title2"

	const { response, result, findReplace } = await batchUpdate(id, null, find, replacement, false)

	console.log(response)
	console.log(result)
	console.log(findReplace)
}

const _updateValues = async (id) => {
	const range = "'Test'!A1:B2"
	const values = [['title1', 'title2'], ['title3', 'title4']]

	const { response, result, err } = await updateValues(id, range, values)

	console.log(response)
	console.log(result)
	console.log(err)
}

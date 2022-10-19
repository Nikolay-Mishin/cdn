import { config } from '../../../config/config.js'
import { log } from '../../../modules/dom.js'
import { getData } from '../../../modules/ajax.js'

const { sheets } = config.api
const { maxCases } = sheets
const { root, cases } = config.path.data
const casesPath = `/${root}/${cases}`

export const getCase = async (id = null, cat = null) => {
	const data = await getData(casesPath)

	console.log(data)

	if (id || cat) {

	}

	return data
}

const result = {}
let cat = ''

export const createCase = async (values) => {
	console.log(values)

	for (let row of values) {
		console.log(cat)

		// set row
		row = createCase.row || values.shift()
		delete createCase.row

		console.log(row)

		// set categoty
		if (row.length <= 1) {
			cat = row.shift() || createCase.cat
			delete createCase.cat
			result[cat] = {}
		}
		else {
			const title = row.pop()

			console.log(title)

			row = values.shift()

			console.log(row)

			const preconditions = row.pop()

			console.log(preconditions)

			row = values.shift()

			console.log(row)

			const set = setSteps(values, row)

			console.log(set)

			const { id, steps } = set

			result[cat][id] = { id, title, preconditions, steps }

			console.log(result)
		}
	}

	console.log(result)

	return result
}

function getSteps() {
	const { id, steps } = setSteps

	delete setSteps.id
	delete setSteps.steps

	console.log({ id, steps })

	return { id, steps }
}

function setId(id, cond = false) {
	if (!id) return null
	const parsed = parseInt(id, 10)
	cond = cond || id < 0 || id !== parsed.toString()
	id = id !== parsed.toString() ? id : parsed
	return !cond ? id : Object.keys(result[cat]).pop() + 1
}

function setSteps(values, row) {
	row = values.shift()

	console.log(row)

	console.log(this)

	if (!row) return getSteps()

	let caseId = row.shift()

	console.log(caseId)

	if (row.length === 0) createCase.cat = caseId

	caseId = setId(caseId, caseId.length > maxCases.length)

	console.log(setSteps.id)

	setSteps.id = setSteps.id || caseId

	console.log(caseId)
	console.log(setSteps.id)

	if (row.length === 0 || caseId === 'ID' || (typeof caseId === 'number' && setSteps.steps)) {
		createCase.row = row
		console.log(row)

		const steps = getSteps()

		console.log(caseId)
		console.log(setSteps.id)

		setSteps.id = caseId

		console.log(setSteps.id)

		return steps
	}

	let stepId = setId(row.shift() || null)

	console.log(stepId)

	console.log(setSteps.steps)

	console.log(row)

	Object.assign(setSteps.steps = setSteps.steps || {}, {
		[stepId]: {
			id: stepId,
			desc: row.shift() || '',
			data: row.shift() || '',
			ER: row.shift() || '',
			AR: row.shift() || ''
		}
	})

	console.log(setSteps.steps)

	return setSteps(values, row)
}

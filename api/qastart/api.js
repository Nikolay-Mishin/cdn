import { config } from '../../config/config.js'
import { getAll, get, getByClass, addEvent, getById, B, createEl } from '../../modules/dom.js'
import { click } from '../../modules/events.js'
import { concat } from '../../modules/array.js'
import { getData } from '../../modules/ajax.js'
import { trimExt } from '../../modules/FS.js'

/**
 * await import('https://cdn/api/qastart/api.js')
 * (await import('https://cdn-4xd.pages.dev/api/qastart/api.js')).setForm()
 * (await import('https://cdn-4xd.pages.dev/api/qastart/api.js')).getForm()
 */

const { host } = config
const { root } = config.path.data
const { root: qaraTMS, repos } = config.path.data.api.qaraTMS
const { root: rootServer, saveFile } = config.path.server
const saveFilePath = `${rootServer}/${saveFile}`

const form = get('form')
const inputs = getAll('input', form)
const submit = get('[type="submit"]', form)

console.log(form)
console.log(inputs)
console.log(submit)

export const init = async () => {
	const { data } = await getRepo('sy')
	console.log(data)

	const signup = get('[href = "/signup"]')
	console.log(signup)

	addEvent('click', (e) => {
		e.preventDefault()
		console.log(e)
	}, signup)

	click(signup)
}

export const getRepo = async (repo) => {
	const file = `${host}/${root}/${qaraTMS}/${repos[repo]}`
	console.log(file)
	return await getData(file)
}

export const setForm = () => {
	init()
}

export const getForm = () => {
	const formData = {}

	const labelClass = 'fl-formlabel'
	const checkboxClass = 'fl-checkbox'
	const localeClass = 'select__single-value'
	const selectClass = 'select__control'
	const menuClass = 'select__menu'
	const optionClass = 'select__option'
	const selectContainerClass = 'select__input-container'

	const errInputClass = 'fl-input-error'
	const errCheckboxClass = 'fl-checkbox-error'
	const errTextClass = 'fl-error_text'
	const errShortClass = 'fl-alert-shortened'

	const labels = getByClass(labelClass)
	const checkboxs = concat(getByClass(checkboxClass))
	const locale = getByClass(localeClass)[0] || null
	let localeId = null
	const select = getByClass(selectClass)[0] || null
	const selectContainer = getByClass(selectContainerClass)[0] || null

	const errorsInput = getByClass(errInputClass)
	const errorsCheckbox = concat(getByClass(errCheckboxClass))

	console.log(labels)
	console.log(checkboxs)
	console.log(locale)
	console.log(select)
	console.log(selectContainer)

	const cb = get('[type="checkbox"]', form)
	const localeInput = getById('locale')

	console.log(cb)
	console.log(localeInput)

	click(localeInput)
	//click(select)
	//click(selectContainer)

	let options = []

	if (select && selectContainer) {
		addEvent('click', (e) => {
			console.log(e)
			console.log(e.target)
			const menu = getByClass(menuClass)[0] || null
			console.log(menu)
			if (!menu) return
			const _options = concat(getByClass(optionClass, menu))
			console.log(_options)
			_options.forEach((opt) => options.push(opt.innerText))
			console.log(options)
		}, select)
	}

	console.log(errorsInput)
	console.log(errorsCheckbox)

	inputs.forEach((input, i) => {
		const { id, type, value, checked } = input
		const label = labels[i].innerText
		const checkboxId = checkboxs.indexOf(input.parentNode)
		const _id = id || `${type}${checkboxId}`
		const _value = id ? (id !== 'locale' ? value : locale?.innerText) : checked
		const checkboxError = errorsCheckbox.find(() => errorsCheckbox.includes(input.parentNode)) ? 'required' : ''
		const error = getByClass(errTextClass, input.parentNode)[0]?.innerText || checkboxError

		console.log({ _id, type, _value, error })

		if (id === 'locale') localeId = i

		formData[_id] = { label, type, value: _value, error }
	})

	formData.error = getByClass(errShortClass)[0]?.innerText || ''

	console.log(formData)

	return { inputs, locale, select, options }
}

import { log, get, getById } from '../../../modules/dom.js'

export const hideButton = (b) => (typeof b !== 'string' ? b : get(b)).style.visibility = 'hidden'
export const showButton = (b) => (typeof b !== 'string' ? b : get(b)).style.visibility = 'visible'

export const hideAllButtons = (id) => getById(id).querySelectorAll('button').forEach((b) => hideButton(b))
export const showAllButtons = (id) => getById(id).querySelectorAll('button').forEach((b) => showButton(b))

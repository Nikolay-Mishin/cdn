import { getByClass } from '../../modules/dom.js'
import { findDuplicates } from '../../modules/find.js'

/**
 * await import('https://cdn/api/docuForce/api.js')
 * (await import('https://cdn/api/docuForce/api.js')).init()
 */

const tableClass = 'fl-detailed_table_sidebar-column'

export const init = async () => {
	const columnList = [...getByClass(tableClass)].map((item) => item.innerText)
	console.log(findDuplicates(columnList))
}

import { isInt } from '../../../modules/int.js'

export const getCountCases = (data) => {
	let count = 0
	const keys = Object.keys(data).filter((k) => isInt(k))
	keys.forEach((k) => {
		const keys = Object.keys(data[k])
		const idList = keys.filter((k) => isInt(k))
		count += idList.length
	})
	return count
}

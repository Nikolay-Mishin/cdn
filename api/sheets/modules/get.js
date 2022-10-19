import { log, getById } from '../../../modules/dom.js'

export const getValues = async (spreadsheetId, range) => {
	let response;

	try {
		response = await gapi.client.sheets.spreadsheets.values.get({
			spreadsheetId: spreadsheetId,
			range: range,
		});
	} catch (e) {
		const { error } = e.result
		getById('content').innerText = error.message;
		return { e, error };
	}

	const result = response.result;
	const values = result.values;
	const numRows = values ? values.length : 0;
	console.log(`${numRows} rows retrieved.`);

	return { response, result, values, numRows };
}

export const batchGetValues = async (spreadsheetId, _ranges) => {
	let response;

	let ranges = [
		// Range names ...
	];
	ranges = _ranges;

	try {
		response = await gapi.client.sheets.spreadsheets.values.batchGet({
			spreadsheetId: spreadsheetId,
			ranges: ranges,
		});
	} catch (response) {
		const { error } = response.result
		getById('content').innerText = error.message;
		return { response, error };
	}

	const result = response.result;
	const valueRanges = result.valueRanges;
	console.log(`${valueRanges.length} ranges retrieved.`);

	return { response, result, values, numRows };
}

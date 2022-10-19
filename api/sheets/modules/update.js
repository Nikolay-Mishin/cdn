import { log, getById } from '../../../modules/dom.js'

export const batchUpdate = async (spreadsheetId, title, find, replacement, allSheets = true) => {
	let response;

	const requests = [];

	// Change the spreadsheet's title.
	if (title) {
		requests.push({
			updateSpreadsheetProperties: {
				properties: {
					title: title,
				},
				fields: 'title',
			},
		});
	}

	// Find and replace text.
	if (find && replacement) {
		requests.push({
			findReplace: {
				find: find,
				replacement: replacement,
				allSheets: allSheets,
			},
		});
	}

	// Add additional requests (operations) ...
	const body = { requests: requests };

	console.log(body)

	try {
		response = await gapi.client.sheets.spreadsheets.batchUpdate({
			spreadsheetId: spreadsheetId,
			resource: body,
		});
	} catch (response) {
		const { error } = response.result
		getById('content').innerText = error.message;
		return { response, error };
	}

	const result = response.result
	const findReplace = result.replies.pop().findReplace;
	console.log(`${findReplace.occurrencesChanged} replacements made.`);

	return { response, result, findReplace };
}

export const updateValues = async (spreadsheetId, range, _values, valueInputOption = "RAW") => {
	let response;

	let values = [
		[
			// Cell values ...
		],
		// Additional rows ...
	];
	values = _values;

	const body = {
		values: values,
	};

	console.log(body);

	try {
		response = await gapi.client.sheets.spreadsheets.values.update({
			spreadsheetId: spreadsheetId,
			range: range,
			valueInputOption: valueInputOption,
			resource: body,
		});
	} catch (response) {
		const { error } = response.result
		getById('content').innerText = error.message;
		return { response, error };
	}

	const result = response.result;
	console.log(`${result.updatedCells} cells updated.`);

	return { response, result };
}

export const batchUpdateValues = async (spreadsheetId, range, _values, valueInputOption = "RAW") => {
	let response;

	let values = [
		[
			// Cell values ...
		],
		// Additional rows ...
	];
	values = _values;
	const data = [];
	data.push({
		range: range,
		values: values,
	});
	// Additional ranges to update.

	const body = {
		data: data,
		valueInputOption: valueInputOption,
	};

	console.log(body);

	try {
		response = await gapi.client.sheets.spreadsheets.values.batchUpdate({
			spreadsheetId: spreadsheetId,
			resource: body,
		});
	} catch (response) {
		const { error } = response.result
		getById('content').innerText = error.message;
		return { response, error };
	}

	const result = response.result;
	console.log(`${result.totalUpdatedCells} cells updated.`);

	return { response, result };
}

export const appendValues = async (spreadsheetId, range, _values, valueInputOption = "RAW") => {
	let response;

	let values = [
		[
			// Cell values ...
		],
		// Additional rows ...
	];
	values = _values;
	const body = {
		values: values,
	};

	console.log(body);

	try {
		response = await gapi.client.sheets.spreadsheets.values.append({
			spreadsheetId: spreadsheetId,
			range: range,
			valueInputOption: valueInputOption,
			resource: body,
		});
	} catch (response) {
		const { error } = response.result
		getById('content').innerText = error.message;
		return { response, error };
	}

	const result = response.result;
	console.log(`${result.updates.updatedCells} cells appended.`);

	return { response, result };
}

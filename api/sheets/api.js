// config
import { config } from '../../config/config.js'
import { log } from '../../../modules/dom.js'
// modules API
import { getValues, batchGetValues } from './modules/get.js'
import { batchUpdate, updateValues, batchUpdateValues, appendValues } from './modules/update.js'
import { hideAllButtons, showAllButtons, hideButton, showButton } from './modules/buttons.js'

export { getValues, batchGetValues, batchUpdate, updateValues, batchUpdateValues, appendValues }

const { sheets } = config.api

/* exported gapiLoaded */
/* exported gisLoaded */
/* exported handleAuthClick */
/* exported handleSignoutClick */

// TODO(developer): Set to client ID and API key from the Developer Console
// TODO: Update placeholder with desired API key.
// TODO: Update placeholder with desired client ID.
const { API_KEY, CLIENT_ID, DISCOVERY_DOC, SCOPES, example } = sheets

// Discovery doc URL for APIs used by the quickstart
//const DISCOVERY_DOC = 'https://sheets.googleapis.com/$discovery/rest?version=v4';

// Authorization scopes required by the API;
// multiple scopes can be included, separated by spaces.
// TODO: Authorize using one of the following scopes:
//   'https://www.googleapis.com/auth/drive'
//   'https://www.googleapis.com/auth/drive.file'
//   'https://www.googleapis.com/auth/drive.readonly'
//   'https://www.googleapis.com/auth/spreadsheets'
//   'https://www.googleapis.com/auth/spreadsheets.readonly'
//const SCOPES = 'https://www.googleapis.com/auth/spreadsheets';

let tokenClient;
let gapiInited = false;
let gisInited = false;

hideAllButtons('buttonsAPI');

document.getElementById('authorize_button').innerText = 'gapi loading...';
showButton('#authorize_button');

/**
 * Callback after api.js is loaded.
 */
export function gapiLoaded() {
	gapi.load('client', intializeGapiClient);
}

/**
 * Callback after the API client is loaded. Loads the
 * discovery doc to initialize the API.
 */
async function intializeGapiClient() {
	await gapi.client.init({
		apiKey: API_KEY,
		discoveryDocs: [DISCOVERY_DOC],
	});
	gapiInited = true;
	maybeEnableButtons();
}

/**
 * Callback after Google Identity Services are loaded.
 */
export function gisLoaded() {
	tokenClient = google.accounts.oauth2.initTokenClient({
		client_id: CLIENT_ID,
		scope: SCOPES,
		callback: '', // defined later
	});
	gisInited = true;
	maybeEnableButtons();
}

/**
 * Enables user interaction after all libraries are loaded.
 */
function maybeEnableButtons() {
	if (gapiInited && gisInited) {
		document.getElementById('authorize_button').innerText = 'Authorize';
	}
}

/**
 *  Sign in the user upon button click.
 */
export async function handleAuthClick(cb) {
	tokenClient.callback = async (resp) => {
		if (resp.error !== undefined) {
			throw (resp);
		}
		document.getElementById('authorize_button').innerText = 'Refresh';
		showAllButtons('buttonsAPI');
		if (example) await listMajors();
		await cb()
	};

	if (gapi.client.getToken() === null) {
		// Prompt the user to select a Google Account and ask for consent to share their data
		// when establishing a new session.
		await tokenClient.requestAccessToken({ prompt: 'consent' });
	} else {
		// Skip display of account chooser and consent dialog for an existing session.
		await tokenClient.requestAccessToken({ prompt: '' });
	}
}

/**
 *  Sign out the user upon button click.
 */
export function handleSignoutClick() {
	const token = gapi.client.getToken();
	if (token !== null) {
		google.accounts.oauth2.revoke(token.access_token);
		gapi.client.setToken('');
		document.getElementById('content').innerText = '';
		document.getElementById('authorize_button').innerText = 'Authorize';
		hideButton('#signout_button');
	}
}

/**
 * Print the names and majors of students in a sample spreadsheet:
 * https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
 */
async function listMajors() {
	let response;
	try {
		// Fetch first 10 files
		response = await gapi.client.sheets.spreadsheets.values.get({
			spreadsheetId: '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms',
			range: 'Class Data!A2:E',
		});
	} catch (err) {
		document.getElementById('content').innerText = err.message;
		return;
	}
	const range = response.result;
	if (!range || !range.values || range.values.length == 0) {
		document.getElementById('content').innerText = 'No values found.';
		return;
	}
	// Flatten to string to display
	const output = range.values.reduce(
		(str, row) => `${str}${row[0]}, ${row[4]}\n`,
		'Name, Major:\n');
	document.getElementById('content').innerText = output;
}

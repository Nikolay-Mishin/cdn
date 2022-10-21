import { log } from './dom.js'
import { headers } from './http.js'
import { jsonParse } from './json.js'
import { tryCatch } from './tryCatch.js'
import { isJson } from './type.js'

export const getData = async (url, _data = '', method = 'GET', toJson = true, _headers = {}, mode = null) => {
	const file = await ajax(url, _data, method, toJson, _headers, mode)
	const { data } = file
	return data
}

export const ajax = async (url, data = '', method = 'GET', toJson = true, _headers = {}, mode = null) => {
	const { ContentType } = headers

	method = data ? 'POST' : method

	const init = { method, headers: _headers }

	if (mode) init.mode = mode // no-cors — Предотвращает, чтобы метод был чем-либо иным, кроме HEAD, GET или POST
	if (data) {
		init.headers = {
			'Content-Type': ContentType.json
		}
		init.body = toJson ? JSON.stringify(data) : data
	}

	console.log(data)
	console.log(init)

	return await tryCatch(async () => {
		const response = await fetch(url, init)
		console.log(response)

		// разбираем (парсим) ответ
		const result = await parseResponse(response)

		console.log(result)
		console.log(typeof result)

		return { response, data: result }
	})
}

/**
 * gatherResponse awaits and returns a response body as a string.
 * Use await gatherResponse(..) in an async function to get the response body
 * @param {Response} response
 */
export async function parseResponse(response) {
	const { headers } = response
	const contentType = headers.get('content-type') || '' // 'application/json'
	console.log(contentType)
	const parse = async (type = 'text') => await response[type]()
	//return contentType == 'application/json' ? await parse('json') : await parse()
	const result = await parse('text')
	const json = jsonParse(result)
	return json || result
}

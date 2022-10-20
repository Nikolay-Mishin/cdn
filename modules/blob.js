import { log } from './dom.js'

export let createBlob = (data, type = 'application/json') => {
	// создаем файл
	const blob = new Blob(
		// сериализуем данные
		[type == 'application/json' ? JSON.stringify(data) : data], {
			type
		}
	)
	// проверяем
	console.log(blob)
	return blob
}

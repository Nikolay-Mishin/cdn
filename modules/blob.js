import { config } from '../config/config.js'
import { log } from './dom.js'
import { createLink } from './link.js'
import { fileName, trimExt } from './FS.js'

const { root, data: dataPath } = config.path.data

export let createBlob = (data, path = `${root}/${dataPath}`) => {
	// создаем файл
	const blob = new Blob(
		// сериализуем данные
		[JSON.stringify(data)], {
		type: 'application/json'
	})

	// проверяем
	console.log(blob)
	/*
		{
			"1": "foo",
			"2": "bar",
			"3": "baz"
		}
	*/
	// то, что доктор прописал

	// создаем элемент "a"
	createLink(blob, path)
}

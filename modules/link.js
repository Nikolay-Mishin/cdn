import { log, createEl, get } from './dom.js'
import { fileName } from './FS.js'

export let createLink = (blob, path, linkName = 'DOWNLOAD DATA', target = 'body') => {
	const name = fileName(path)
	console.log(name)
	// создаем элемент "a"
	const link = createEl('a')
	// привязываем атрибут "href" тега "a" к созданному файлу
	link.setAttribute('href', URL.createObjectURL(blob))
	// атрибут "download" позволяет скачивать файлы, на которые указывает ссылка
	// значение этого атрибута - название скачиваемого файла
	link.setAttribute('download', name)
	// текстовое содержимое ссылки
	link.textContent = linkName
	// помещаем элемент в контейнер с классом "main"
	get(target).append(link)
	// удаляем ссылку на файл
	URL.revokeObjectURL(blob)
}

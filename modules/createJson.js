import { config } from '../config/config.js'
import { log, getAll } from './dom.js'
import { createLink } from './link.js'

const { root, data: dataPath } = config.path.data

export let createJson = (ev) => {
	// любая кнопка в форме имеет тип "submit" по умолчанию, т.е. служит для отправки формы на сервер
	// отправка формы влечет за собой перезагрузку страницы
	// нам это не нужно, поэтому отключаем стандартное поведение
	ev.preventDefault()

	// находим все инпуты
	const inputs = getAll('input')

	// извлекаем значения, введенные пользователем
	// это можно сделать разными способами
	// такой способ показался мне наиболее оптимальным

	// здесь мы реализуем нечто похожее на метод "chunk" библиотеки "lodash"
	// значения нечетных инпутов (первого, третьего и пятого) - ключи
	// помещаем их в подмассивы в качестве первых элементов
	// значения четных инпутов (второго, четвертого и шестого) - значения
	// помещаем их в подмассивы в качестве вторых элементов
	const arr = []
	for (let i = 0; i < inputs.length; ++i) {
		arr.push([inputs[i].value, inputs[++i].value])
	}

	// получаем массив, состоящий из трех подмассивов
	console.log(arr)
	/*
		[
			["1", "foo"]
			["2", "bar"]
			["3", "baz"]
		]
	*/

	// преобразуем массив подмассивов в объект
	const data = Object.fromEntries(arr)

	// проверяем
	console.log(data)
	/*
		{
			1: "foo"
			2: "bar"
			3: "baz"
		}
	*/

	// создаем файл
	createLink(data, `${root}/${dataPath}`)
}

import { config } from '../config/config.js'
import { log } from './dom.js'
import { getData as ajaxGetData } from './ajax.js'

const { saveFile } = config.path.server
const { root, data: dataPath } = config.path.data

// с помощью async..await получаем данные и выводим их в консоль в виде таблицы
export const getData = async (url = `${root}/${dataPath}`) => {
	const data = await ajaxGetData(url)
}

// с помощью async..await отправляем данные и выводим их в консоль в виде таблицы
export const setData = async (_data = {}, file = `${root}/${dataPath}`, url = `${saveFile}`, method = 'POST') => {
	const data = await ajaxGetData(url, { file, data: _data }, method)
}

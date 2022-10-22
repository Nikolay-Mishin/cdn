import { config } from '../config/config.js'
import { getData as ajaxGetData } from '../modules/ajax.js'
import { IIFE } from '../modules/IIFE.js'
import { log, B, addEvent, get, getById, createEl } from '../modules/dom.js'
import { ready } from '../modules/ready.js'
import { createJson } from '../modules/createJson.js'
import { getData, setData } from '../modules/data.js'
import { appendScreenshot } from '../modules/screenshot.js'
import { getCase } from '../api/sheets/modules/case.js'
import { $import } from '../modules/import.js'
import { observeDemo } from '../modules/demo.js'
import { getCountCases } from '../api/qaraTMS/modules/getCountCases.js'
import { fileExt, fileName, trimExt } from '../modules/FS.js'
import { setState } from '../modules/history.js'
import { createBlob } from '../modules/blob.js'

const { root, test } = config.path.data
const data = await ajaxGetData(`/${root}/${test}`)
const { root: qaraTMS, repos } = config.path.data.api.qaraTMS
const { root: serverRoot, saveFile } = config.path.server
const saveFilePath = `${serverRoot}/${saveFile}`

IIFE(({ log, D, B }) => console.log(B))

addEvent("DOMContentLoaded", ready);

// находим кнопку с классом "create-json" и обрабатываем нажатие этой кнопки
// { once: true } автоматически удаляет обработчик после первого использования
// повторный клик приводит к перезагрузке страницы
addEvent('click', createJson, get('.create-json'), { once: true })

// находим кнопку (которая на самом деле ссылка) с классом "get-data" и обрабатываем ее нажатие
addEvent('click', () => getData(), get('.get-data'))

// находим кнопку (которая на самом деле ссылка) с классом "set-data" и обрабатываем ее нажатие
addEvent('click', () => setData(data), get('.set-data'))

B.append('Hello!')

addEvent('click', async () => await appendScreenshot(), getById('get_images'))

let _imports = await $import()
console.log(_imports)

observeDemo()

await getCase()

let result = {}
const repoList = Object.values(repos)
repoList.forEach(async (repo, i) => {
	const { data } = await ajaxGetData(`${root}/${qaraTMS}/${repo}`)
	const count = getCountCases(data)
	result[trimExt(repo, false)] = { count, data }
	if (repoList.length == ++i) console.log(result)
})

const testParser = await ajaxGetData(saveFilePath, { url: 'https://ya.ru/', action: 'getPage' })
const testParser2 = await ajaxGetData(saveFilePath, { url: 'https://gitlab.com/qastart/web1-group/frontend/-/issues/89', action: 'getPage' })
const { content } = testParser.html.data
getById('testParser').innerHTML = content
//getById('testParser2').innerHTML = testParser2.html.data.content

setState('index.html', { id: 'historyContent' })
setState('page.php', { id: 'historyContent' })
setState('page2.php', { update: true, id: 'historyChange' })

const url = 'https://gitlab.com/qastart/web1-group/frontend/upl…c7454c4ec01416a95abdc39f68c50/__screen__br_16.jpg'
const ext = fileExt(url)
//createBlob({ url })

console.log(ext)

const div = createEl('div')
div.innerHTML = content

console.log(div)
console.log(div.querySelector('title'))

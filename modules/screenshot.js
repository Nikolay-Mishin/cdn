import { config } from '../config/config.js'
import { log, B, getById } from './dom.js'
import { getData } from './ajax.js'
import { createLink } from './link.js'
import { getImgData, imgToBlob } from './img.js'

const { root, saveFile, upload } = config.path.server
const saveFilePath = `${root}/${saveFile}`

export const createScreenshot = async (canvas, type = 'png', save = true) => {
	const name = Date.now()
	const file = `${upload}/${name}.${type}`
	// Export the canvas to its data URI representation
	const { data, base64image } = getImgData(canvas, type)

	console.log(file)

	if (save) await getData(saveFilePath, { file, data, action: 'saveImg' })

	return { name, base64image, file }
}

export const appendScreenshot = async (target = B, type = 'png', save = true) => {
	await html2canvas(target).then(async (canvas) => {
		const { name, base64image } = await createScreenshot(canvas, type, save)
		const { img } = imgToBlob(({ blob }) => {
			console.log(blob)
			// Generate file download
			createLink(blob, `${name}.${type}`, type, '#screenshot', 'DOWNLOAD IMG')
		}, base64image, canvas)

		getById('result').appendChild(canvas)
		getById('screen').append(img)
	})
}

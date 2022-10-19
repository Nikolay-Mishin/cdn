import { config } from '../config/config.js'
import { log, B, getById, createEl } from './dom.js'
import { getData } from './ajax.js'
import { createLink } from './link.js'

const { root, saveFile, upload } = config.path.server
const saveFilePath = `${root}/${saveFile}`

export const createScreenshot = async (canvas, type = 'png', save = true) => {
	const imgType = `image/${type}`
	const name = Date.now()
	const file = `${upload}/${name}.${type}`

	console.log(file)

	// Export the canvas to its data URI representation
	const base64image = canvas.toDataURL(imgType)

	console.log(base64image)

	const data = getImgData(base64image, imgType)

	console.log(data)

	if (save) await getData(saveFilePath, { file, data, action: 'saveImg' })

	return { name, base64image, file }
}

export const appendScreenshot = async (target = B, type = 'png', save = true) => {
	await html2canvas(target).then(async (canvas) => {
		const { name, base64image } = await createScreenshot(canvas, type, save)

		const resultEl = getById('result')

		resultEl.appendChild(canvas)

		const img = createEl('img')
		img.src = base64image
		getById("screen").append(img)

		// Export canvas as a blob
		canvas.toBlob((blob) => {
			// Generate file download
			createLink(blob, `${name}.${type}`, 'DOWNLOAD IMG', '#screenshot')
		})
	})
}

export const getImgData = (base64image, type = 'image/png') => base64image.replace(`data:${type};base64,`, '')

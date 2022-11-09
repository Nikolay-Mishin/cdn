import { log, createEl } from './dom.js'
import { fileName } from './FS.js'

export const getImgData = (canvas, type = 'png') => {
	type = `image/${fileName(type.replace('jpg', 'jpeg'))}`
	canvas = canvas.localName === 'img' ? imgToCanvas(canvas) : canvas
	const base64image = canvas.toDataURL(type)
	const data = base64image.replace(`data:${type};base64,`, '')

	console.log(type)
	//console.log(base64image)
	//console.log(data)

	return { data, base64image }
}

export const imgToBlob = (cb = () => { }, src, c) => {
	const srcIsImg = typeof src === 'object' && src.localName === 'img'
	const img = srcIsImg ? src : new Image
	let data, base64image

	img.onload = function () {
		console.log(this)
		c = c || imgToCanvas(this)
		// get image as blob
		c.toBlob((blob) => {
			const imgData = getImgData(c, blob.type)
			data = imgData.data
			base64image = imgData.base64image
			src = src || base64image
			cb({ blob, data, base64image })
		}/*, "image/jpeg", 0.75*/)
	}
	img.src = srcIsImg ? img.src : src

	return { img, data, base64image }
}

export const imgToCanvas = (img) => {
	const c = createEl("canvas")
	const ctx = c.getContext("2d")
	c.width = img.naturalWidth // update canvas size to match image
	c.height = img.naturalHeight
	ctx.drawImage(img, 0, 0) // draw in image
	return c
}

import { log, createEl } from './dom.js'

export const getImgData = (canvas, type = 'png') => {
	type = `image/${type.replace('jpg', 'jpeg')}`
	canvas = canvas.localName === 'img' ? imgToCanvas(canvas) : canvas
	const base64image = canvas.toDataURL(type)
	const data = base64image.replace(`data:${type};base64,`, '')

	console.log(type)
	console.log(base64image)
	console.log(data)

	return { data, base64image }
}

export const imgToBlob = (cb = () => { }, src, c) => {
	const srcIsImg = typeof src === 'object' && src.localName === 'img'
	const img = srcIsImg ? src : new Image

	img.onload = function () {
		console.log(this)
		c = c || imgToCanvas(this)
		// get image as blob
		c.toBlob((blob) => {
			src = src || getImgData(c, blob.type)
			cb(blob)
		}/*, "image/jpeg", 0.75*/)
	}
	img.src = srcIsImg ? img.src : src

	return img
}

export const imgToCanvas = (img) => {
	const c = createEl("canvas")
	const ctx = c.getContext("2d")
	c.width = img.naturalWidth // update canvas size to match image
	c.height = img.naturalHeight
	ctx.drawImage(img, 0, 0) // draw in image
	return c
}

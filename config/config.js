import { tryCatch } from '../modules/tryCatch.js'

const path = 'config/config.json'
const rootList = ['/', 'https://cdn/', 'https://cdn-4xd.pages.dev/']

const getConfig = async (root) => {
	console.log('try: ' + root + path)
	return await tryCatch(async () => {
		const response = await fetch(root + path)
		const config = await response.json()
		console.log(config)
		return config
	}, async (e) => rootList.length ? await getConfig(rootList.shift()) : { e })
}

export const config = await getConfig(rootList.shift())

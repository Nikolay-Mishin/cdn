import { jsonParse } from './json.js'

export const isObj = (item) => typeof item === 'object'
export const isJson = (item) => jsonParse(item) ? true : false

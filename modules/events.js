import { config } from '../config/config.js'

const { click: configClick, observer: configObserver } = config.core

const configClickDef = {
	view: window
}

export const click = (target, opts = configClick) => {
	return target.dispatchEvent(new MouseEvent('click', Object.assign(configClickDef, configClick, opts)))
}

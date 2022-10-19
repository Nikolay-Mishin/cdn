export const timeout = (cb, time = 1000, ...args) => {
	return setTimeout(cb, 1000, ...args)
}

export const interval = (cb, time = 1000, count = 1, ...args) => {
	// повторить с интервалом 2 секунды
	const timerId = setInterval(cb, time)
	// остановить вывод через 5 секунд
	const cbStop = () => { clearInterval(timerId); console.log('stop'); }
	return count ? setTimeout(cbStop, time * count, ...args) : timerId
}

export const clear = (timerId) => {
	return clearTimeout(timerId)
}

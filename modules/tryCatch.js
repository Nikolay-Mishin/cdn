export const tryCatch = async (_try, _catch = (e) => { return { e } }) => {
	try { return await _try() }
	catch (e) {
		console.log(e)
		return await _catch(e)
	}
}

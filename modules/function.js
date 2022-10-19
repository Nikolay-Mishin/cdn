export function callFuncByName(funcName, context = window, ...args) {
    let namespaces = funcName.split(".")
    let func = namespaces.pop()
    namespaces.forEach((namespace) => {
        context = context[namespace]
    })
    return context[func].apply(context, args)
}

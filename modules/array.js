export const concat = (...list) => [].concat.apply([], ...list)
export const push = (arr, v) => v.length ? arr.push(...v) : arr

import { concat } from './array.js'

// log = arg => console.log(arg) - здесь мы используем параметры по умолчанию
// это позволит вызывать log как log
export let log = arg => console.log(arg)
export let table = arg => console.table(arg)
// это позволит обращаться к document и document.body как к D и B, соответственно
export let D = document
export let html = document.html
export let H = document.head
export let B = document.body

export let addEvent = (query, cb, target = D, opts = {}) => target.addEventListener(query, cb, opts)

export let getAll = (query, target = D) => target.querySelectorAll(query)
export let get = (query, target = D) => target.querySelector(query)
export let getById = (id, target = D) => target.getElementById(id)
export let getByClass = (id, target = D) => target.getElementsByClassName(id)

export let createEl = (el, target = D) => target.createElement(el)

export const filterNodes = (nodes, ...filter) => concat(nodes).filter((node) => filter.includes(node.localName))
export const withoutNodes = (nodes, ...without) => concat(nodes).filter((node) => without.includes(node.localName))

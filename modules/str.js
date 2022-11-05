export const camelize = (str) => {
	return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
		return index === 0 ? word.toLowerCase() : word.toUpperCase();
	}).replace(/\s+/g, '');
}

export const capitalizeFirstLetter = ([first, ...rest], locale = navigator.language) =>
	first === undefined ? '' : first.toLocaleUpperCase(locale) + rest.join('')

//camelize("EquipmentClass name");
//camelize("Equipment className");
//camelize("equipment class name");
//camelize("Equipment Class Name");
// all output "equipmentClassName"

//console.log(
//	capitalizeFirstLetter(''), // [empty string]
//	capitalizeFirstLetter('foo'), // Foo
//	capitalizeFirstLetter("𐐶𐐲𐑌𐐼𐐲𐑉"), // "𐐎𐐲𐑌𐐼𐐲𐑉" (correct!)
//	capitalizeFirstLetter("italya", 'tr') // İtalya" (correct in Turkish Latin!)
//)

/**
 * Parses kebab-case to camelCase.
 * @param {string} text - The original text.
 * @returns {string} Camel case text.
 */
function parseKebabToCamelCase(text) {
	let newText = '';
	let nextCharUpper = false;
	for (let i = 0; i < text.length; i++) {
		const currentChar = text[i];
		if (currentChar == '-') {
			nextCharUpper = true;
			continue;
		}
		if (currentChar == ' ') {
			continue;
		}
		if (nextCharUpper) {
			newText += currentChar.toUpperCase();
			nextCharUpper = false;
			continue;
		}
		newText += currentChar.toLowerCase();
	}
}
/**
 * Temporarily assigns a class to an element for the set duration of time.
 * The timer is reset when called again by storing timeout IDs in data-* attributes.
 * @param {HTMLElement} element - The element to apply the class to.
 * @param {string} className - Name of the class to switch.
 * @param {number} duration - The amount of time in ms.
 */
async function tempClassForTime(element, className, duration) {
	const dataAttribute = `${parseKebabToCamelCase(className)}TimeoutId`;
	let timeoutId = element.dataset[dataAttribute];
	element.classList.add(className);
	if (element.dataset[dataAttribute] != '') {
		clearTimeout(Number(timeoutId));
	}
	timeoutId = setTimeout(() => {
		element.classList.remove(className);
		element.dataset[dataAttribute] = '';
	}, duration);
	element.dataset[dataAttribute] = `${timeoutId}`;
}
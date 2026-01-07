//# Constants
/**
 * Class for hiding an element.
 */
const HIDDEN_CLASS = 'hidden';
/**
 * Class for making an element red and bold.
 */
const WARNING_HIGHLIGHT_CLASS = 'warning-highlight';
/**
 * Class for making an element scale to 105%.
 */
const WARNING_POP_CLASS = 'warning-pop';

//# Functions

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
	return newText;
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

/**
 * Creates an URL object from an image file. Throws if it's not a file or an image.
 * @param {File} file The file to create the URL object from.
 * @returns {string} The newly created object URL.
 */
function createImageObjectUrl(file) {
	if (!(file instanceof File || !file.type.startsWith('image/'))) {
		throw new Error('File is not correct.');
	}
	let objectUrl = URL.createObjectURL(file);
	return objectUrl;
}

/**
 * Uses an invoked object URL of an image to compare its aspect ratio with a target one.
 * @param {string} objectUrl The object URL of the tested image.
 * @param {number} targetRatio The target ratio tested.
 * @returns {bool} `true` when the image's aspect ratio matches the target one (0.01 percision), else `false`.
 */
async function validateImageAspectRatio(objectUrl, targetRatio) {
	return new Promise((resolve, reject) => {
		const image = new Image();
		image.src = objectUrl;
		image.addEventListener('error', () => {
			reject('image error');
		});
		image.addEventListener('load', () => {
			const imageRatio = image.width / image.height;
			if (Math.abs(imageRatio - targetRatio) < 0.01) {
				resolve(true);
			} else {
				resolve(false);
			}
		});
	});
}

/**
 * Activates or deactivates element.
 * @param {Element} element Element to alter.
 * @param {boolean} $state When `true` remove attribute `disabled`, when `false` add it.
 */
function setElementEnabled(element,$state = true) {
    if ($state) {
        element.removeAttribute('disabled');
    } else {
        element.setAttribute('disabled','');
    }
}

/**
 * Activates or deactivates element's readonly property.
 * @param {Element} element Element to alter.
 * @param {boolean} $state When `true` remove attribute `readonly`, when `false` add it.
 */
function setElementEditable(element,$state = true) {
    if ($state) {
        element.removeAttribute('readonly');
    } else {
        element.setAttribute('readonly','');
    }
}
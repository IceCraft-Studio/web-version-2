const MARKDOWN_URL = 'https://api.github.com/markdown';
const LIST_CATEGORIES_ENDPOINT = `${window.location.origin}/~dobiapa2/api/internal/projects/list-categories.php`

const BUTTON_EDIT_ID = 'btn-edit-article';
const BUTTON_PREVIEW_ID = 'btn-preview-article';
const ARTICLE_PREVIEW_ID = 'md-preview';
const ARTICLE_EDIT_ID = 'md-input';
const GALLERY_UPLOAD_ZONE_ID = 'gallery-upload-zone';
const GALLERY_UPLOAD_SELECT = `#${GALLERY_UPLOAD_ZONE_ID} > input[type='file']`;
const GALLERY_PREVIEW_ID = 'gallery-preview';
const CATEGORY_SELECT_ID = 'input-category';
const ALLOWED_IMAGE_TYPES = Object.freeze([
	'image/jpeg',
	'image/png',
	'image/gif',
	'image/webp',
]);
const ALLOWED_ASPECT_RATIO = 16 / 9;
const MAX_ALLOWED_FILE_SIZE_MB = 8;

const HIDDEN_CLASS = 'hidden';
const COPIED_CLASS = 'copied';
const WARNING_HIGHLIGHT_CLASS = 'warning-highlight';
const WARNING_POP_CLASS = 'warning-pop';

async function main() {
	let galleryIndex = -1;
	let markdownEdited = false;
	const elements = {
		descriptionInput: document.querySelector('textarea#input-description'),
		markdownInput: document.getElementById(ARTICLE_EDIT_ID),
		markdownOutput: document.getElementById(ARTICLE_PREVIEW_ID),
		previewButton: document.getElementById(BUTTON_PREVIEW_ID),
		editButton: document.getElementById(BUTTON_EDIT_ID),
		galleryUploadInput: document.querySelector(GALLERY_UPLOAD_SELECT),
		dropUploadInput: document.getElementById(GALLERY_UPLOAD_ZONE_ID),
		galleryPreview: document.getElementById(GALLERY_PREVIEW_ID),
		categorySelect: document.getElementById(CATEGORY_SELECT_ID),
	};
	// Dynamically fetch available categories
	fillCategories(elements.categorySelect);
	// Remove Empty Category on selection
	elements.categorySelect.addEventListener('change', removeEmptyCategory);
	// Markdown preview functionality
	elements.editButton.addEventListener('click', (e) => {
		showEdit(e, elements);
	});
	elements.previewButton.addEventListener('click', (e) => {
		showPreview(e, elements, markdownEdited);
	});
	elements.markdownInput.addEventListener('change', () => {
		markdownEdited = true;
	});
	// Prevent newlines in description
	elements.descriptionInput.addEventListener('input', () => {
		elements.descriptionInput.value =
			elements.descriptionInput.value.replace(/\r?\n|\r/g, '');
	});
	// Gallery
	galleryUpdate(elements, galleryIndex);
	// Drag n drop make it work
	window.addEventListener('drop', (e) => {
		if ([...e.dataTransfer.items].some((item) => item.kind === 'file')) {
			e.preventDefault();
		}
	});
	//TODO - INPUT VALIDATION
	//TODO - Refactor the logic, shorten, strighten, maybe use html templates
	//TODO - Consider making Drag-n-drop animations
}

main();

function removeEmptyCategory(event) {
	if (event.target.value != '') {
		event.target.querySelector('option[value=""]').remove();
		event.target.removeEventListener('change', removeEmptyCategory);
	}
}

async function fillCategories(selectElement) {
	selectElement.insertAdjacentHTML('beforeend', `<option value=""></option>`);

	const reqHeaders = new Headers();
	reqHeaders.set('Accept', 'application/json');
	reqHeaders.set('Content-Type', 'application/json');

	const options = {
		method: 'POST',
		headers: reqHeaders,
		body: '',
	};
	const req = new Request(LIST_CATEGORIES_ENDPOINT, options);

	const response = await fetch(req);
	const jsonData = await response.json();

	for (const category in jsonData['categories']) {
		selectElement.insertAdjacentHTML(
			'beforeend',
			`<option value="${category.id}">${category.displayName}</option>`
		);
	}
}

async function galleryUpdate(elements, galleryIndex) {
	if (elements.dropUploadInput != null) {
		elements.dropUploadInput.classList.add(HIDDEN_CLASS);
		elements.dropUploadInput.removeAttribute('id');
	}
	galleryIndex++;
	elements.galleryPreview.insertAdjacentHTML(
		'beforeend',
		generateGalleryItem(galleryIndex)
	);
	elements.dropUploadInput = document.getElementById(GALLERY_UPLOAD_ZONE_ID);
	elements.galleryUploadInput = document.querySelector(GALLERY_UPLOAD_SELECT);

	elements.galleryUploadInput.addEventListener('change', async (e) => {
		await processGalleryFileUpload(
			e.target.files[0],
			elements,
			galleryIndex
		);
	});
	elements.dropUploadInput.addEventListener('dragover', (e) => {
		e.preventDefault();
	});
	elements.dropUploadInput.addEventListener('drop', async (e) => {
		e.preventDefault();
		if (
			await processGalleryFileUpload(
				e.dataTransfer.files[0],
				elements,
				galleryIndex
			)
		) {
			elements.galleryUploadInput.files = e.dataTransfer.files;
		}
	});
}

async function processGalleryFileUpload(file, elements, galleryIndex) {
	//Check file size and type
	const sizeMB = file.size / 1000 ** 2; //MB (1000) not MiB (1024)!
	if (
		sizeMB > MAX_ALLOWED_FILE_SIZE_MB ||
		!ALLOWED_IMAGE_TYPES.includes(file.type)
	) {
		elements.galleryUploadInput.value = null;
		let sizeWarningElement = document.querySelector(
			`#${GALLERY_UPLOAD_ZONE_ID} > .size-warning`
		);
		setTimeout(() => {
			sizeWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
			tempClassForTime(sizeWarningElement, WARNING_POP_CLASS, 750);
		}, 500);
		return false;
	}
	// Create image object url and ensure the correct aspect ratio
	const imageObjectUrl = await createImageObjectUrl(file);
	const validAspectRatio = await validateImageAspectRatio(
		imageObjectUrl,
		ALLOWED_ASPECT_RATIO
	);
	if (!validAspectRatio) {
		URL.revokeObjectURL(imageObjectUrl);
		elements.galleryUploadInput.value = null;
		let ratioWarningElement = document.querySelector(
			`#${GALLERY_UPLOAD_ZONE_ID} > .ratio-warning`
		);
		setTimeout(() => {
			ratioWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
			tempClassForTime(ratioWarningElement, WARNING_POP_CLASS, 750);
		}, 500);
		return false;
	}
	// Finish procedure if checks are fine
	await galleryUpdate(elements, galleryIndex);
	await insertFileUrl(imageObjectUrl, elements, galleryIndex);
	return true;
}

async function createImageObjectUrl(file) {
	if (!(file instanceof File || !file.type.startsWith('image/'))) {
		throw new Error('File is not correct.');
	}
	let objectUrl = URL.createObjectURL(file);
	return objectUrl;
}

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

async function insertFileUrl(objectUrl, elements, galleryIndex) {
	let imgElement = document.querySelector(
		`#${GALLERY_PREVIEW_ID} > li[data-gallery-index="${galleryIndex}"] img`
	);
	imgElement?.setAttribute('src', objectUrl);
	// Unhide the image and make it clickable
	let imgButtonElement = document.querySelector(
		`#${GALLERY_PREVIEW_ID} > li[data-gallery-index="${galleryIndex}"] button`
	);
	imgButtonElement?.classList.remove(HIDDEN_CLASS);
	imgButtonElement.addEventListener('click', (e) => {
		e.preventDefault();
		navigator.clipboard.writeText(objectUrl.slice(5)); // remove blob: as GitHub Markdown API destroyes it
		tempClassForTime(imgButtonElement, COPIED_CLASS, 4000);
	});
	let hiddenInput = document.querySelector(
		`#${GALLERY_PREVIEW_ID} > li[data-gallery-index="${galleryIndex}"] input[type="hidden"]`
	);
	hiddenInput?.setAttribute('value', objectUrl);
}

function generateGalleryItem(i) {
	return `
<li class="gallery-image" data-gallery-index="${i}">
  <div class="field">
    <label for="gallery-caption-${i}">Image #${i + 1}</label>
    <input id="gallery-caption-${i}" name="gallery-caption[${i}]" type="text" placeholder="Caption of Image #${
		i + 1
	}.">
  </div>
  <label id="gallery-upload-zone" for="gallery-upload-${i}">
	<span class="size-warning">The image must be JPEG, PNG, GIF or WEBP of 8MB at most!</span>
	<span class="ratio-warning">The image needs to have 16:9 aspect ratio!</span>
    <span>Drop images here, or click to upload.</span>
    <input id="gallery-upload-${i}" name="gallery-upload[${i}]" type="file" accept=".jpeg,.jpg,.png,.gif,.webp"/>
  </label>
  <button class="gallery-container hidden">
    <img alt="Image #${i + 1}">
  </button>
  <div class="gallery-include-checkbox">
    <label for="gallery-include-${i}">Image #${i + 1} in gallery:</label>
    <input id="gallery-include-${i}" name="gallery-include[${i}]" type="checkbox" checked>
  </div>
  <input name="gallery-browser-url[${i}]" type="hidden">
</li>`;
}

function replaceUploadZone() {
	const html = ``;
}

/**
 * Takes user's Markdown from a TextArea element and outputs it to a content element.
 * @param {HTMLTextAreaElement} inputElement HTML textarea element with user's input.
 * @param {HTMLElement} outputElement Element to output HTML generated from the MD into.
 */
async function generatePreview(inputElement, outputElement) {
	let markdownData = inputElement.value;
	const htmlResult = markdownGithub(markdownData);
	outputElement.innerHTML = htmlResult;
	// fix markdown removing images (added comments cause it's a lil' strange behavior)
	outputElement
		.querySelectorAll('a > img[data-canonical-src]')
		.forEach((img) => {
			// GitHub MD wraps a link around a broken image
			const a = img.parentElement;
			// The links has the original URL stored in 'data-canonical-src' attribute
			const canonical = img.getAttribute('data-canonical-src');
			// Check if it actually points to our website
			if (!canonical.startsWith(`${location.protocol}//${location.host}`))
				return;
			// Add the blob prefix to the img src to make the browser interpret it as url object
			img.src = 'blob:' + canonical;
			// Replace the link with the img alone
			a.replaceWith(img);
		});
}

/**
 * Calls GitHub Markdown API and returns a safe HTML.
 * @param {string} data Markdown data sent in the request's body
 * @returns {string} Safe HTML generated from the input MD.
 */
async function markdownGithub(data) {
	const reqHeaders = new Headers();
	reqHeaders.set('Accept', 'application/vnd.github+json');
	reqHeaders.set('Content-Type', 'application/json');
	reqHeaders.set('X-GitHub-Api-Version', '2022-11-28');

	const options = {
		method: 'POST',
		headers: reqHeaders,
		body: JSON.stringify({
			text: data,
			mode: 'gfm',
		}),
	};
	const req = new Request(MARKDOWN_URL, options);

	const response = await fetch(req);
	return await response.text();
}

/**
 * The action assigned to a button used to show the edit field.
 * @param {Event} event The event (usually a click) on the "Edit" element.
 * @param {*} elements Interal object storing important reused elements.
 * @param {boolean} markdownEdited If markdown is edited, preview is regenerated.
 */
function showEdit(event, elements) {
	event.target.disabled = true;
	elements.previewButton.disabled = false;
	elements.markdownOutput.classList.add(HIDDEN_CLASS);
	elements.markdownInput.classList.remove(HIDDEN_CLASS);
}

/**
 * The action assigned to a button used to show the preview.
 * @param {Event} event The event (usually a click) on the "Preview" element.
 * @param {*} elements Interal object storing important reused elements.
 * @param {boolean} markdownEdited If markdown is edited, preview is regenerated.
 */
function showPreview(event, elements, markdownEdited) {
	event.target.disabled = true;
	if (markdownEdited) {
		generatePreview(elements.markdownInput, elements.markdownOutput);
	}
	elements.editButton.disabled = false;
	elements.markdownOutput.classList.remove(HIDDEN_CLASS);
	elements.markdownInput.classList.add(HIDDEN_CLASS);
	markdownEdited = false;
}

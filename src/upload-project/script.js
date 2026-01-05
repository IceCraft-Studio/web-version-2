const MARKDOWN_URL = 'https://api.github.com/markdown';
const LIST_CATEGORIES_ENDPOINT = `${window.location.origin}/~dobiapa2/api/internal/projects/list-categories.php`;
const VERIFY_SLUG_ENDPOINT = `${window.location.origin}/~dobiapa2/api/internal/projects/verify-slug.php`;

const PREVIEW_TITLE_SELECT = '.project-card .project-part h3';
const PREVIEW_DESC_SELECT = '.project-card  .project-part p.description';
const PREVIEW_IMAGE_SELECT = '.project-card .project-part img';
const BUTTON_EDIT_ID = 'btn-edit-article';
const BUTTON_PREVIEW_ID = 'btn-preview-article';
const ARTICLE_PREVIEW_ID = 'md-preview';
const ARTICLE_EDIT_ID = 'md-input';
const GALLERY_UPLOAD_ZONE_ID = 'gallery-upload-zone';
const GALLERY_UPLOAD_SELECT = `#${GALLERY_UPLOAD_ZONE_ID} > input[type='file']`;
const GALLERY_PREVIEW_ID = 'gallery-preview';
const LINK_ADDER_ID = 'link-adder';
const FILE_ADDER_ID = 'file-adder';
const CATEGORY_SELECT_ID = 'input-category';
const ADD_LINK_BTN_SELECT = '#link-adder button.add-another';
const ADD_FILE_BTN_SELECT = '#file-adder button.add-another';
const ALLOWED_IMAGE_TYPES = Object.freeze([
	'image/jpeg',
	'image/png',
	'image/gif',
	'image/webp',
]);
const ALLOWED_THUMB_IMAGE_TYPES = Object.freeze([
	'image/jpeg',
	'image/png',
	'image/webp',
]);
const ALLOWED_ASPECT_RATIO = 16 / 9;
const MAX_ALLOWED_IMAGE_SIZE_MB = 8;
const MAX_ALLOWED_UPLOAD_SIZE_MB = 30;
const MAX_FILE_AMOUNT = 5;
const MAX_LINK_AMOUNT = 5;
const MAX_GALLERY_AMOUNT = 10;

const EDIT_INSERTED_CLASS = 'edit-inserted';
const COPIED_CLASS = 'copied';

async function main() {
	let uploadIndexes = {
		gallery: -1,
		files: -1,
		links: -1,
	};
	let markdownEdited = false;
	const elements = {
		titleInput: document.querySelector('input#input-title'),
		descriptionInput: document.querySelector('textarea#input-description'),
		thumbnailInput: document.querySelector('input#input-thumbnail'),
		slugPrefix: document.querySelector(
			"div.prefix-container > label[for='input-slug']"
		),
		slugInput: document.querySelector('input#input-slug'),
		markdownInput: document.getElementById(ARTICLE_EDIT_ID),
		markdownOutput: document.getElementById(ARTICLE_PREVIEW_ID),
		previewButton: document.getElementById(BUTTON_PREVIEW_ID),
		editButton: document.getElementById(BUTTON_EDIT_ID),
		galleryUploadInput: document.querySelector(GALLERY_UPLOAD_SELECT),
		dropUploadInput: document.getElementById(GALLERY_UPLOAD_ZONE_ID),
		galleryPreview: document.getElementById(GALLERY_PREVIEW_ID),
		categorySelect: document.getElementById(CATEGORY_SELECT_ID),
		previewTitle: document.querySelector(PREVIEW_TITLE_SELECT),
		previewDescription: document.querySelector(PREVIEW_DESC_SELECT),
		previewThumbnail: document.querySelector(PREVIEW_IMAGE_SELECT),
		linkAdder: document.getElementById(LINK_ADDER_ID),
		fileAdder: document.getElementById(FILE_ADDER_ID),
		addLinkButton: document.querySelector(ADD_LINK_BTN_SELECT),
		addFileButton: document.querySelector(ADD_FILE_BTN_SELECT),
	};
	const uploadCounter = {
		gallery: elements.galleryPreview?.querySelectorAll(`.${EDIT_INSERTED_CLASS}`)?.length ?? 0,
		links: elements.linkAdder?.querySelectorAll(`.${EDIT_INSERTED_CLASS}`)?.length ?? 0,
		files: elements.galleryPreview?.querySelectorAll(`.${EDIT_INSERTED_CLASS}`)?.length ?? 0,
	};

	// Dynamically fetch available categories from the backend
	fillCategories(elements.categorySelect);
	// Handle changing category
	elements.categorySelect.addEventListener('change', (e) => {
		handleCategoryUpdate(e, elements);
	});
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
	// Title card update
	elements.titleInput.addEventListener('input', () => {
		elements.previewTitle.textContent = elements.titleInput.value;
	});
	// Description correction and card update
	elements.descriptionInput.addEventListener('input', () => {
		elements.descriptionInput.value =
			elements.descriptionInput.value.replace(/\r?\n|\r/g, '');
		elements.previewDescription.textContent =
			elements.descriptionInput.value;
	});
	// Thumbnail validation and card update
	elements.thumbnailInput.addEventListener('change', async (e) => {
		let file = e.target.files[0];
		if ((await validateThumbnail(file, elements)) === false) {
			return;
		}
		let objectUrl = await createImageObjectUrl(file);
		elements.previewThumbnail.src = objectUrl;
	});
	// Input validation in #input-slug
	elements.slugInput.addEventListener('input', () => {
		elements.slugInput.value = correctSlugInput(elements.slugInput.value);
	});
	// Gallery
	galleryUpdate(elements, uploadIndexes, uploadCounter);
	// Drag n drop make it work
	window.addEventListener('drop', (e) => {
		if ([...e.dataTransfer.items].some((item) => item.kind === 'file')) {
			e.preventDefault();
		}
	});
	//Add link and file buttons
	elements.addLinkButton.addEventListener('click', (e) => {
		e.preventDefault();
		if (uploadCounter.links >= MAX_LINK_AMOUNT) {
			return;
		}
		uploadIndexes.links++;
		uploadCounter.links++;
		elements.addLinkButton.parentElement.insertAdjacentHTML('beforebegin',generateLinkAdder(uploadIndexes.links));
		setElementActivation(elements.addLinkButton,uploadCounter.links < MAX_LINK_AMOUNT);
	});
	elements.addFileButton.addEventListener('click', (e) => {
		e.preventDefault();
		if (uploadCounter.files >= MAX_FILE_AMOUNT) {
			return;
		}
		uploadIndexes.files++;
		uploadCounter.files++;
		elements.addFileButton.parentElement.insertAdjacentHTML('beforebegin',generateFileAdder(uploadIndexes.files));
		document.getElementById(`input-file-upload-${uploadIndexes.links}`).addEventListener((e) => {
			validateFileUpload(e.target.files[0],elements,e.target);
		})
		setElementActivation(elements.addFileButton,uploadCounter.files < MAX_FILE_AMOUNT);
	});

}

main();

/**
 * Returns corrected version of the slug string.
 * @param {string} slug - The slug string
 * @returns {string} Corrected string.
 */
function correctSlugInput(slug) {
	return slug
		.toLowerCase()
		.replaceAll(/(-$)|(^-)|[^a-z0-9-]/g, '')
		.replaceAll(/-+/g, '-');
}

function handleCategoryUpdate(event, elements) {
	if (event.target.value != '') {
		event.target.querySelector('option[value=""]')?.remove();
		elements.slugPrefix.textContent = `/${event.target.value}/`;
		elements.slugInput.removeAttribute('disabled');
	}
}

/**
 * Fetches categories from our API and fills them as OPTION elements.
 * @param {HTMLSelectElement} selectElement Select element to fill with OPTION elements.
 */
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

	for (const category of jsonData['categories']) {
		selectElement.insertAdjacentHTML(
			'beforeend',
			`<option value="${category.id}">${category.name}</option>`
		);
	}
}

async function galleryUpdate(elements, uploadIndexes, uploadCounter) {
	if (elements.dropUploadInput != null) {
		elements.dropUploadInput.classList.add(HIDDEN_CLASS);
		elements.dropUploadInput.removeAttribute('id');
	}
	if (uploadCounter.gallery >= MAX_GALLERY_AMOUNT) {
		elements.dropUploadInput = null;
		elements.galleryUploadInput = null;
		return;
	}
	uploadIndexes.gallery++;
	uploadCounter.gallery++;
	elements.galleryPreview.insertAdjacentHTML(
		'beforeend',
		generateGalleryItem(uploadIndexes.gallery)
	);
	elements.dropUploadInput = document.getElementById(GALLERY_UPLOAD_ZONE_ID);
	elements.galleryUploadInput = document.querySelector(GALLERY_UPLOAD_SELECT);

	elements.galleryUploadInput.addEventListener('change', async (e) => {
		await processGalleryFileUpload(
			e.target.files[0],
			elements,
			uploadIndexes,
			uploadCounter
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
				uploadIndexes,
				uploadCounter
			)
		) {
			elements.galleryUploadInput.files = e.dataTransfer.files;
		}
	});
}

async function processGalleryFileUpload(
	file,
	elements,
	uploadIndexes,
	uploadCounter
) {
	//Check file size and type
	const sizeMB = file.size / 1000 ** 2; //MB (1000) not MiB (1024)!
	if (
		sizeMB > MAX_ALLOWED_IMAGE_SIZE_MB ||
		!ALLOWED_IMAGE_TYPES.includes(file.type)
	) {
		elements.galleryUploadInput.value = null;
		let sizeWarningElement = document.querySelector(
			`#${GALLERY_UPLOAD_ZONE_ID} > .size-warning`
		);
		sizeWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(sizeWarningElement, WARNING_POP_CLASS, 750);
		return false;
	}
	// Create image object url and ensure the correct aspect ratio
	const imageObjectUrl = createImageObjectUrl(file);
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
		ratioWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(ratioWarningElement, WARNING_POP_CLASS, 750);
		return false;
	}
	// Finish procedure if checks are fine
	await galleryUpdate(elements, uploadIndexes, uploadCounter);
	await insertGalleryFileUrl(imageObjectUrl, elements, uploadIndexes.gallery);
	return true;
}

async function validateThumbnail(file, elements) {
	//Check file size and type
	const sizeMB = file.size / 1000 ** 2; //MB (1000) not MiB (1024)!
	if (
		sizeMB > MAX_ALLOWED_IMAGE_SIZE_MB ||
		!ALLOWED_THUMB_IMAGE_TYPES.includes(file.type)
	) {
		elements.thumbnailInput.value = null;
		let sizeWarningElement = document.querySelector(`.thumbnail-size`);
		sizeWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(sizeWarningElement, WARNING_POP_CLASS, 750);
		return false;
	}
	// Create image object url and ensure the correct aspect ratio
	const imageObjectUrl = createImageObjectUrl(file);
	const validAspectRatio = await validateImageAspectRatio(
		imageObjectUrl,
		ALLOWED_ASPECT_RATIO
	);
	if (!validAspectRatio) {
		URL.revokeObjectURL(imageObjectUrl);
		elements.thumbnailInput.value = null;
		let ratioWarningElement = document.querySelector(`.thumbnail-ratio`);
		ratioWarningElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(ratioWarningElement, WARNING_POP_CLASS, 750);
		return false;
	}
	URL.revokeObjectURL(imageObjectUrl);
	return true;
}

function validateFileUpload(file, elements, target) {
	const sizeMB = file.size / 1000 ** 2; //MB (1000) not MiB (1024)!
	if (sizeMB > MAX_ALLOWED_UPLOAD_SIZE_MB) {
		target.value = null;
		return false;
	}
	return true;
}

/**
 * Verifies with the server if the slug is available.
 * @param {string} slug - The slug to verify.
 * @returns {bool} `true` when the slug is available.
 */
async function verifySlugAvailability(slug, category) {
	let reqHeaders = new Headers();
	reqHeaders.set('Accept', 'application/json');
	reqHeaders.set('Content-Type', 'application/json');

	const options = {
		method: 'POST',
		headers: reqHeaders,
		body: JSON.stringify({
			slug: username,
		}),
	};
	const req = new Request(VERIFY_SLUG_ENDPOINT, options);

	const response = await fetch(req);
	const jsonData = await response.json();

	return jsonData?.available;
}

async function insertGalleryFileUrl(objectUrl, elements, galleryIndex) {
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

//## Dynamic HTML Generators
/**
 * Generates HTML for a gallery input.
 * @param {number} i Index of the input field.
 * @returns
 */
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
  <input name="gallery-browser-url[${i}]" type="hidden">
</li>`;
}

/**
 * Generates HTML for a link adder input.
 * @param {number} i Index of the input field.
 * @returns
 */
function generateFileAdder(i) {
	return `
<li data-file-index="${i}">
	<div class="field">
		<p>File #${i + 1} to download the project.</p>
        <label for="input-file-upload-${i}">File Upload:</label>
        <input type="file" name="file-upload[${i}]" id="input-file-upload-${i}">
        <label for="input-file-name-${i}">Display Name:</label>
        <input type="text" name="file-name[${i}]" id="input-file-name-${i}" maxlength="96">
    </div>
</li>`;
}

/**
 * Generates HTML for a gallery input.
 * @param {number} i Index of the input field.
 * @returns
 */
function generateLinkAdder(i) {
	return `
<li data-link-index="${i}">
    <div class="field">
        <p>Link #${i + 1} to download the project.</p>
        <label for="input-link-url-${i}">URL:</label>
        <input type="text" name="link-url[${i}]" id="input-link-url-${i}" maxlength="200">
        <label for="input-link-name-${i}">Display Name:</label>
        <input type="text" name="link-name[${i}]" id="input-link-name-${i}" maxlength="96">
    </div>
</li>`;
}

/**
 * Takes user's Markdown from a TextArea element and outputs it to a content element.
 * @param {HTMLTextAreaElement} inputElement HTML textarea element with user's input.
 * @param {HTMLElement} outputElement Element to output HTML generated from the MD into.
 */
async function generatePreview(inputElement, outputElement) {
	let markdownData = inputElement.value;
	const htmlResult = await markdownGithub(markdownData);
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

//## Article Preview Functions

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

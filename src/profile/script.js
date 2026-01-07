const INPUT_PROFILE_PICTURE_ID = 'input-profile-picture';
const INPUT_DELETE_PROFILE_PICTURE_ID = 'input-delete-profile-picture';
const INPUT_PASSWORD_NEW_ID = 'input-password-new';
const INPUT_PASSWORD_CONFIRM_ID = 'input-password-confirm';
const SUBMIT_BUTTON_SELECTOR = "form input[type='submit']";
const PROFILE_PICTURE_CONTAINER_ID = 'profile-picture-container';
const PROFILE_PICTURE_HINT_ID = 'profile-picture-hint';

const ALLOWED_IMAGE_TYPES = Object.freeze([
	'image/jpeg',
	'image/png',
	'image/webp',
]);
const ALLOWED_ASPECT_RATIO = 1 / 1;
const MAX_ALLOWED_IMAGE_SIZE_MB = 15;

let globalSubmitActivate = false;

async function main () {
    const elements = {
        containerProfilePicture: document.getElementById(PROFILE_PICTURE_CONTAINER_ID),
        inputProfilePicture: document.getElementById(INPUT_PROFILE_PICTURE_ID),
        inputDeleteProfilePicture: document.getElementById(INPUT_DELETE_PROFILE_PICTURE_ID),
        submitButton: document.querySelector(SUBMIT_BUTTON_SELECTOR),
		profilePictureHintElement: document.getElementById(PROFILE_PICTURE_HINT_ID)
    }
    elements.inputDeleteProfilePicture.addEventListener('input',(e) => {
        if (e.target.checked) {
            elements.containerProfilePicture.classList.add('disabled');
            elements.inputProfilePicture.disabled = true;
        } else {
            elements.containerProfilePicture.classList.remove('disabled');
            elements.inputProfilePicture.disabled = false;
        }
    });
    elements.containerProfilePicture.addEventListener('click',(e) => {
        e.preventDefault();
        elements.inputProfilePicture.click();
    })
    elements.inputProfilePicture.addEventListener('input', async (e) => {
        let file = e.target.files[0];
        if (file != null) {
            let objectUrl = await processProfilePicture(file,elements);
			if (objectUrl === false) {
				return;
			}
            elements.containerProfilePicture.querySelector('img')?.setAttribute('src',objectUrl);
        }
    })
    document.querySelectorAll('input').forEach((element) => {
        element.addEventListener('input', () => {
            if (!globalSubmitActivate) {
                elements.submitButton.disabled = false;
                globalSubmitActivate = true;
            }
        }) 
    });
}

function validatePassword(password) {
	let isGood = password.length >= 8 && password.match(/[0-9]/) != null && password.match(/[a-z]/) != null && password.match(/[A-Z]/) != null;
	return isGood;
}

async function processProfilePicture(file, elements) {
    const sizeMB = file.size / 1000 ** 2; //MB (1000) not MiB (1024)!
	if (
		sizeMB > MAX_ALLOWED_IMAGE_SIZE_MB ||
		!ALLOWED_IMAGE_TYPES.includes(file.type)
	) {
		elements.inputProfilePicture.value = null;
		elements.profilePictureHintElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(elements.profilePictureHintElement, WARNING_POP_CLASS, 750);
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
		elements.inputProfilePicture.value = null;
		elements.profilePictureHintElement?.classList.add(WARNING_HIGHLIGHT_CLASS);
		tempClassForTime(elements.profilePictureHintElement, WARNING_POP_CLASS, 750);
		return false;
	}
    return imageObjectUrl;
}

main();
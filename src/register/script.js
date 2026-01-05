const NAME_USERNAME = 'username';
const NAME_PASSWORD = 'password';
const NAME_CONFIRM = 'confirm-password';
const CHECK_CIRCLE_URL = '/~dobiapa2/assets/icons/check-circle.svg'
const BANG_CIRCLE_URL = '/~dobiapa2/assets/icons/exclamation-circle.svg'

const VERIFY_USERNAME_ENDPOINT = `${window.location.origin}/~dobiapa2/api/internal/users/verify-username.php`;

const CIRCLE_SVG = {};

async function main() {
	CIRCLE_SVG.bang = await(await fetch(BANG_CIRCLE_URL)).text();
	CIRCLE_SVG.check = await(await fetch(CHECK_CIRCLE_URL)).text();
	const elements = {
		inputUsername: document.querySelector(`input[name=${NAME_USERNAME}]`),
		inputPassword: document.querySelector(`input[name=${NAME_PASSWORD}]`),
		inputConfirm: document.querySelector(`input[name=${NAME_CONFIRM}]`),
        submitButton: document.querySelector(`input[type=submit]`),
		usernameHelp: document.querySelector(`#username-hint`),
		form: document.querySelector(`form`),
	};
	const validValues = {
		username: validateUsername(elements.inputUsername.value,elements.inputUsername.dataset.available),
		password: validatePassword(elements.inputPassword.value),
		confirm: confirmPassword(
			elements.inputConfirm.value,
			elements.inputPassword.value
		),
	};
	let usernameVerificationTimer = null;

    validateForm(validValues,elements);

	elements.inputPassword.addEventListener('input', (e) => {
		validValues.password = validatePassword(e.target.value);
		validValues.confirm = confirmPassword(
			elements.inputConfirm.value,
			e.target.value
		);
        validateForm(validValues,elements);
	});
	elements.inputUsername.addEventListener('input', (e) => {
		let newValue = correctUsernameInput(e.target.value);
		if (newValue.length < e.target.value.length) {
			tempClassForTime(elements.usernameHelp,'warning-highlight', 4000);
		}
		e.target.value = newValue;
		usernameVerificationTimer = queueAvailibilityVerification(newValue,elements,validValues, usernameVerificationTimer);

		validValues.username = validateUsername(newValue,e.target.dataset.available);
        validateForm(validValues,elements);
	});
	elements.inputConfirm.addEventListener('input', (e) => {
		validValues.confirm = confirmPassword(
			e.target.value,
			elements.inputPassword.value
		);
        validateForm(validValues,elements);
	});
}

function validateForm(validValues, elements) {
    for (key in validValues) {
        if (!validValues[key]) {
            elements.submitButton.disabled = true;
            return false;
        }
    }
    elements.submitButton.disabled = false;
    return true;

}

/**
 * Prepares verification 1.5 seconds in advance and clears any previously prepared verification.
 * @param {string} username username to verify
 * @param {object} elements All referenced elements.
 * @param {object} validValues Used to validate the form.
 * @param {number} timer Previous verification timer.
 * @returns {number} New timer.
 */
function queueAvailibilityVerification(username,elements,validValues,timer) {
	clearTimeout(timer);
	let newTimer = setTimeout(async () => {
		let isAvailable = await verifyUsernameAvailability(username);
		elements.inputUsername.dataset.available = isAvailable ? '1' : '0';
		let isValid = validateUsername(username,elements.inputUsername.dataset.available);
		validValues.username = isAvailable && isValid;
		validateForm(validValues,elements);
	},1500);
	return newTimer;
}

/**
 * Verifies with the server if the username is available.
 * @param {string} username - The username to verify.
 * @returns {bool} `true` when the username is available.
 */
async function verifyUsernameAvailability(username) {
	let reqHeaders = new Headers();
	reqHeaders.set('Accept', 'application/json');
	reqHeaders.set('Content-Type', 'application/json');

	const options = {
		method: 'POST',
		headers: reqHeaders,
		body: JSON.stringify({
			username: username
		}),
	};
	const req = new Request(VERIFY_USERNAME_ENDPOINT, options);

	const response = await fetch(req);
	const jsonData = await response.json();

	return jsonData?.available;
}

/**
 * Returns corrected version of the username string.
 * @param {string} username - The username string
 * @returns {string} Corrected string.
 */
function correctUsernameInput(username) {
	return username.toLowerCase().replaceAll(/(-$)|(^-)|[^a-z0-9-]/g,'').replaceAll(/-+/g,'-');
}

function validateUsername(username,available) {
	let isGood = (username.length >= 4 && username.length <= 48 && available == "1");
	setIndicator(NAME_USERNAME, isGood);
	return isGood;
}

function validatePassword(password) {
	let isGood = password.length >= 8 && password.match(/[0-9]/) != null && password.match(/[a-z]/) != null && password.match(/[A-Z]/) != null;
	setIndicator(NAME_PASSWORD, isGood);
	return isGood;
}

function confirmPassword(password1, password2) {
	let isGood = password1 == password2;
	setIndicator(NAME_CONFIRM, isGood);
	return isGood;
}

async function setIndicator(name, isGood) {
    let indicatorElement = document.querySelector(
		`.indicator[data-for=${name}]`
	);
	if (isGood) {
		indicatorElement.classList.add('indication-good');
		indicatorElement.classList.remove('indication-bad');
		indicatorElement.innerHTML = CIRCLE_SVG.check;
	} else {
		indicatorElement.classList.add('indication-bad');
		indicatorElement.classList.remove('indication-good');
		indicatorElement.innerHTML = CIRCLE_SVG.bang;
	}
}

main();

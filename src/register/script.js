const HIDDEN_CLASS = 'hidden';
const NAME_USERNAME = 'username';
const NAME_PASSWORD = 'password';
const NAME_CONFIRM = 'confirm-password';
const CHECK_CIRCLE_URL = '/~dobiapa2/assets/icons/check-circle.svg'
const BANG_CIRCLE_URL = '/~dobiapa2/assets/icons/exclamation-circle.svg'

async function main() {
	const elements = {
		inputUsername: document.querySelector(`input[name=${NAME_USERNAME}]`),
		inputPassword: document.querySelector(`input[name=${NAME_PASSWORD}]`),
		inputConfirm: document.querySelector(`input[name=${NAME_CONFIRM}]`),
        submitButton: document.querySelector(`input[type=submit]`),
		form: document.querySelector(`form`),
	};
	const validValues = {
		username: false,
		password: false,
		confirm: false,
	};

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
		validValues.username = validateUsername(e.target.value);
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

function validatePassword(password) {
	let isGood = password.length >= 8 && password.length <= 128;
	setIndicator(NAME_PASSWORD, isGood);
	return isGood;
}

function validateUsername(username) {
	let isGood = username.length >= 4 && username.length <= 32;
	setIndicator(NAME_USERNAME, isGood);
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
		indicatorElement.classList.remove('indication-bad');
		indicatorElement.classList.add('indication-good');
		indicatorElement.innerHTML = await (
			await fetch(CHECK_CIRCLE_URL)
		).text();
	} else {
		indicatorElement.classList.add('indication-bad');
		indicatorElement.classList.remove('indication-good');
		indicatorElement.innerHTML = await (
			await fetch(BANG_CIRCLE_URL)
		).text();
	}
}

main();

async function main() {
    console.log(window.location.href);
    screenSizeAlert();
    initializeTheme()

    initializeDropdownLinks()

    setThemeIcon(document.documentElement.dataset.theme);

    document.querySelector('#topbar > .theme-toggle').addEventListener('pointerdown', toggleTheme);
    document.querySelector('#topbar > .tri-dash-menu > .dash-menu-toggle').addEventListener('pointerdown', toggleLinkDropdown);
}

main();

function screenSizeAlert() {
	const fontSize = parseInt(getComputedStyle(document.documentElement).fontSize.match(/\d+/));
	const supportedSize = 18 * fontSize;
	if (window.innerWidth >= supportedSize) return; 
	alert(`Your screen size is unsupported!\nYou might experience issues because the width of your window is less than ${Math.round(supportedSize)}px. Try lowering your default font size or resize the browser window to prevent this.\n\nScreen width = ${window.innerWidth}px\nFont size = ${fontSize}px`);
}

// Theme functions

function initializeTheme() {
    const rootDataset = document.documentElement.dataset;
    const savedTheme = localStorage.getItem('web-theme');
    if (savedTheme) {
        rootDataset.theme = savedTheme;
        return;
    }
    if (window?.matchMedia('(prefers-color-scheme: dark)').matches) {
        rootDataset.theme = 'dark';
        localStorage.setItem('web-theme', 'dark');
    } else {
        rootDataset.theme = 'light';
        localStorage.setItem('web-theme', 'light');
    }
}

function setThemeIcon(theme) {
	const iconElement = document.querySelector('#topbar > .theme-toggle > img');
	if (theme === 'dark') {
		iconElement.src = '/~dobiapa2/assets/icons/moon-icon.png';
	}
	if (theme === 'light') {
		iconElement.src = '/~dobiapa2/assets/icons/sun-icon.png';
	}
}

function toggleTheme(eventData) {
    const rootDataset = document.documentElement.dataset;
	let newTheme;
    if (rootDataset.theme === 'light') {
		newTheme = 'dark';
    } else if (rootDataset.theme === 'dark') {
		newTheme = 'light';
    }

	rootDataset.theme = newTheme;
	setThemeIcon(newTheme);
	localStorage.setItem('web-theme', newTheme);
}

// Navigation Dropdown for Mobile

function initializeDropdownLinks() {
	const links = document.querySelector('#topbar > .links-container').innerHTML;
	const dropdownElement = document.querySelector('#topbar > .tri-dash-menu > .links-dropdown');
	dropdownElement.innerHTML = links;
}

function toggleLinkDropdown(eventData) {
    const menuElement = document.querySelector('#topbar > .tri-dash-menu');
    const iconElement = document.querySelector('#topbar > .tri-dash-menu > .dash-menu-toggle > img');
    if (menuElement.classList.contains('open')) {
        menuElement.classList.remove('open');
        iconElement.src = "/~dobiapa2/assets/icons/tri-dash-icon.svg";
    } else {
        menuElement.classList.add('open');
        iconElement.src = "/~dobiapa2/assets/icons/tri-dash-icon-open.svg";
    }
}
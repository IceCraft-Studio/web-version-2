async function main() {
    screenSizeAlert();
    initializeTheme()

    console.log(window.location.href);
}

main();

function screenSizeAlert() {
	const fontSize = parseInt(getComputedStyle(document.documentElement).fontSize.match(/\d+/));
	const supportedSize = 18 * fontSize;
	if (window.innerWidth >= supportedSize) return; 
	alert(`Your screen size is unsupported!\nYou might experience issues because the width of your window is less than ${Math.round(supportedSize)}px. Try lowering your default font size or resize the browser window to prevent this.\n\nScreen width = ${window.innerWidth}px\nFont size = ${fontSize}px`);
}

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
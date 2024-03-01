// Functions to handle the timer and time display
// https://www.educative.io/answers/how-to-create-a-stopwatch-in-javascript

let startTime; // to keep track of the start time
let stopwatchInterval; // to keep track of the interval

// Starts the timer; called when pressing "Start" button
function startStopwatch() {
	if (!stopwatchInterval) {
		startTime = new Date().getTime(); // get the starting time
		stopwatchInterval = setInterval(updateStopwatch, 1000); // update every second
	}
}

// Stops the timer; called when pressing answer button on the final question
function stopStopwatch() {
	clearInterval(stopwatchInterval); // stop the interval
	stopwatchInterval = null; // reset the interval variable
}

// Updates the timer
function updateStopwatch() {
	let currentTime = new Date().getTime(); // get current time in milliseconds
	let elapsedTime = currentTime - startTime; // calculate elapsed time in milliseconds
	let seconds = Math.floor(elapsedTime / 1000) % 60; // calculate seconds
	let minutes = Math.floor(elapsedTime / 1000 / 60); // calculate minutes
	let displayTime = minutes + ":" + pad(seconds); // format display time
	document.querySelector("#display-time").innerText = displayTime; // update the display
	userTime = elapsedTime; // store elapsed time in global for safekeeping
}

// Add a leading zero if the number is less than 10
function pad(number) {
	return (number < 10 ? "0" : "") + number;
}

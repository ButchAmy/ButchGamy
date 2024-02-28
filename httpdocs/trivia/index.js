// API constants
const TRIVIA = "https://opentdb.com/api"; // endpoint for the Open Trivia API
const BUTCHGAMY = "http://localhost:9001/api"; // endpoint for the ButchGamy API
const API_KEY = "65df2c54e895e9.42604372"; // key used for the ButchGamy API

// Parameter constants
const PARAMS = new URLSearchParams(document.location.search);

// Questions array
let questionArray = [];

// Internal global values
let userScore = 0; // User's real score
let userTime = 0; // User's real time (in *milli*seconds)
let correctCount = 0; // Number of correct answers
let wrongCount = 0; // Number of wrong answers

// Functions to perform on page load
fetchCategories();
fetchUsername(PARAMS.get("userId"));

// Start button
document.querySelector("#start").addEventListener("click", async () => {
	// fetch questions
	await fetchQuestions();

	// change header
	document.querySelector("#header-title").classList.add("w3-hide");
	document.querySelector("#header-result").classList.remove("w3-hide");

	// start timer
	startStopwatch();

	// draw first question
	drawQuestion(0);
});

// Fill template with question data; event listeners handled in main functions
async function drawQuestion(index) {
	// Clear display container
	document.querySelector("#display").innerHTML = "";

	if (index == 10) {
		stopStopwatch();
		await handleResults();
		await drawResults();
		return;
	}

	// Retrieve question from array
	let question = questionArray[index];

	// Retrieve template from HTML
	let template = document.querySelector("#question-template").content.cloneNode(true);

	// Shuffle answers
	let answers = shuffleAnswers(question);

	// Fill out template
	template.querySelector("#index").innerHTML = Number(index) + 1;
	template.querySelector("#display-difficulty").innerHTML = question.difficulty.toLowerCase().replace(/(^\s*\w|[\.\!\?]\s*\w)/g, function (c) {
		return c.toUpperCase();
	});
	template.querySelector("#display-category").innerHTML = question.category;
	template.querySelector("#question").innerHTML = question.question;
	template.querySelectorAll("#answer").forEach((element, i) => {
		element.innerHTML = answers[i];
		element.addEventListener("click", () => {
			checkAnswer(element.innerHTML, index);
			drawQuestion(Number(index) + 1);
		});
	});
	template.querySelector("#skip").addEventListener("click", () => {
		drawQuestion(Number(index) + 1);
	});

	// Draw  template
	document.querySelector("#display").appendChild(template);
}

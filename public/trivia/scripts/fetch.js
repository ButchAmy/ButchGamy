// Functions to handle fetching data from APIs

async function fetchCategories() {
	// Fetch categories
	let response = await fetch(TRIVIA + "_category.php");
	let json = await response.json();

	// Sort categories alphabetically
	let categories = json.trivia_categories.sort((a, b) => {
		const nameA = a.name.toUpperCase(); // ignore upper and lowercase
		const nameB = b.name.toUpperCase(); // ignore upper and lowercase
		if (nameA < nameB) {
			return -1;
		}
		if (nameA > nameB) {
			return 1;
		}
		return 0; // if names are equal
	});

	// Populate category select menu
	categories.forEach((category) => {
		let option = document.createElement("option");
		option.value = category.id;
		option.innerText = category.name;
		document.querySelector("#select-category").appendChild(option);
	});
}

async function fetchUsername(userId) {
	// Fetch user
	let response = await fetch(BUTCHGAMY + "/user/" + userId);
	let user = await response.json();

	// Update all username fields
	document.querySelectorAll("#username").forEach((element) => {
		element.innerText = user.username;
	});
}

async function fetchQuestions() {
	// Retrieve selected difficulty and category
	let difficulty = document.querySelector("input[name='select-difficulty']:checked").value;
	let category = document.querySelector("#select-category").value;

	// Fetch questions
	let response = await fetch(TRIVIA + ".php?amount=10&type=multiple&difficulty=" + difficulty + "&category=" + category);
	json = await response.json();

	// Store questions in global
	questionArray = json.results;
}

function shuffleAnswers(question) {
	// Make array of all possible answers
	let answerArray = [question.correct_answer, question.incorrect_answers[0], question.incorrect_answers[1], question.incorrect_answers[2]];

	// Shuffle the array
	// https://www.freecodecamp.org/news/how-to-shuffle-an-array-of-items-using-javascript-or-typescript/
	for (let i = answerArray.length - 1; i > 0; i--) {
		const j = Math.floor(Math.random() * (i + 1));
		[answerArray[i], answerArray[j]] = [answerArray[j], answerArray[i]];
	}

	// Return the array
	return answerArray;
}

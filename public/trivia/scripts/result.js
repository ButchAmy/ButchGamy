// Functions to handle posting results and drawing the result screen

async function handleResults() {
	// Determine final score and time
	let finalScore = userScore;
	let finalTime = Math.floor(userTime / 1000);

	// Assemble form data
	let formData = new FormData();
	formData.append("apiKey", API_KEY);
	formData.append("userId", PARAMS.get("userId"));
	formData.append("hash", PARAMS.get("hash"));
	formData.append("score", finalScore);
	formData.append("time", finalTime);

	let response = await fetch(BUTCHGAMY + "/result", {
		method: "POST",
		body: formData,
	});

	console.log(response);
}

async function drawResults() {
	// Display waiting message
	document.querySelector("#display").innerHTML = "Calculating your result...";

	// Retrieve template from HTML
	let template = document.querySelector("#result-template").content.cloneNode(true);

	// Check difficulty and calculate what a perfect score would be
	let difficulty = questionArray[0].difficulty;
	let perfectScore = getPerfectScore(1000, difficulty);

	// Check if playthrough was single category or any category
	let anyCategory = false;
	for (let index = 0; index < questionArray.length - 1; index++) {
		if (questionArray[index].category != questionArray[index + 1].category) {
			anyCategory = true;
		}
	}

	// Fill out template
	template.querySelector("#correct-count").innerHTML = correctCount;
	template.querySelector("#wrong-count").innerHTML = wrongCount;
	template.querySelector("#final-score").innerHTML = userScore;
	template.querySelector("#commentary").innerHTML = getCommentary(userScore, perfectScore);
	template.querySelector("#play-again").innerHTML = '<a href="' + window.location.href + '">play again</a>';

	// Check for achievements
	if (userScore == perfectScore) {
		if (anyCategory == true) {
			if (await unlockKnowitallAchievement()) {
				template.querySelector("#achievements").classList.remove("w3-hide");
				template.querySelector("#achievement-knowitall").classList.remove("w3-hide");
			}
		}
		if (difficulty == "hard") {
			if (await unlockDoctorateAchievement()) {
				template.querySelector("#achievements").classList.remove("w3-hide");
				template.querySelector("#achievement-doctorate").classList.remove("w3-hide");
			}
		}
	}
	if (correctCount + wrongCount == 0) {
		if (await unlockSocratesAchievement()) {
			template.querySelector("#achievements").classList.remove("w3-hide");
			template.querySelector("#achievement-socrates").classList.remove("w3-hide");
		}
	}

	// Clear display container and display template
	document.querySelector("#display").innerHTML = "";
	document.querySelector("#display").appendChild(template);
}

// Determine the comment to show on the final page
function getCommentary(userScore, perfectScore) {
	if (userScore >= perfectScore) {
		return "That's a PERFECT score! Congratulations!";
	}
	if (userScore >= perfectScore / 2) {
		return "That's a passing grade! Well done!";
	}
	if (userScore > 0) {
		return "I'm sure you'll do better next time!";
	}
	if (userScore == 0) {
		return "Perfectly balanced, as all things should be.";
	}
	if (userScore < 0) {
		return "Wow, you're a special kind of stupid, huh?";
	}
}

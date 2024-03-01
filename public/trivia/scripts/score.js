// Functions to handle scoring and score display

// Check if answer is correct
function checkAnswer(answer, index) {
	if (answer == questionArray[index].correct_answer) {
		updateScore("correct", questionArray[index].difficulty);
	} else {
		updateScore("wrong", questionArray[index].difficulty);
	}
}

// Update the score based on response
function updateScore(result, difficulty) {
	switch (result) {
		case "correct":
			userScore += getAdjustedScore(1000, difficulty);
			correctCount++;
			break;
		case "wrong":
			userScore -= getAdjustedScore(500, difficulty);
			wrongCount++;
			break;
	}
	document.querySelector("#display-score").innerText = userScore; // update the display
}

// Adjust score based on difficulty
function getAdjustedScore(value, difficulty) {
	switch (difficulty) {
		case "easy":
			return value * 0.5;
		case "hard":
			return value * 1.5;
		default:
			return value;
	}
}

// Return what a perfect score would be for the given difficulty
function getPerfectScore(value, difficulty) {
	switch (difficulty) {
		case "easy":
			return value * 10 * 0.5;
		case "hard":
			return value * 10 * 1.5;
		default:
			return value * 10;
	}
}

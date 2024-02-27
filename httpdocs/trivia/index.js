const BUTCHGAMY = "http://localhost:9001/api";
const TRIVIA = "https://opentdb.com/api";
const KEY = "65de47d04a9d93.79624940";

async function fetchCategories() {
	let response = await fetch(TRIVIA + "_category.php");
	let categories = await response.json();
}

async function fetchQuestions(difficulty, category = 0) {
	let response = await fetch(TRIVIA + ".php?amount=10&difficulty=" + difficulty + "&category=" + category);
	let questions = await response.json();
}

/* async function fetchChannelList(currentChannel) {
	let response = await fetch(ENDPOINT + "/channels");
	let channelList = await response.json();
	channelList.forEach((channel) => {
		let option = document.createElement("option");
		option.value = channel.id;
		option.innerText = channel.name;
		option.dataset.passwordProtected = channel.passwordProtected;
		if (channel.passwordProtected) {
			option.innerText += "🔒";
		}
		document.querySelector("#channels").appendChild(option);
	});
	document.querySelector("#channels").value = currentChannel;
} */

function calculateScore(difficulty, correct, wrong) {
	switch (difficulty) {
		case easy:
			return correct * 500 - wrong * 250;
		case medium:
			return correct * 1000 - wrong * 500;
		case hard:
			return correct * 1500 - wrong * 750;
		default:
			return "SCORE ERROR";
	}
}

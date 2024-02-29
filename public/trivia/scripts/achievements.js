// Functions to unlock achievements; called from the result screen

// If quiz complete with perfect score on Any category
async function unlockKnowitallAchievement() {
	let name = "Know-it-all"; // unique identifier
	let description = "Get a perfect score without choosing a specific category.";
	let image = "https://wiki.teamfortress.com/w/images/1/18/Tf_play_game_everyclass.png";

	return await unlockAchievement(name, description, image); // uf true, display notification
}

// If quiz complete with perfect score on Hard difficulty
async function unlockDoctorateAchievement() {
	let name = "Doctorate"; // unique identifier
	let description = "Get a perfect score while playing on the Hard difficulty.";
	let image = "https://wiki.teamfortress.com/w/images/e/e7/Tf_complete_training.png";

	return await unlockAchievement(name, description, image); // uf true, display notification
}

// If quiz complete with 0 correctCount and 0 wrongCount
async function unlockSocratesAchievement() {
	let name = "Socrates 2.0"; // unique identifier
	let description = "The only thing I know is that I know nothing.";
	let image = "https://wiki.teamfortress.com/w/images/8/8a/Tf_medic_top_scoreboard.png";

	return await unlockAchievement(name, description, image); // uf true, display notification
}

// Generic achievement unlock function; should be called only from other achievement functions!
async function unlockAchievement(name, description, image) {
	let formData = new FormData();
	formData.append("apiKey", API_KEY);
	formData.append("userId", PARAMS.get("userId"));
	formData.append("hash", PARAMS.get("hash"));
	formData.append("name", name);
	formData.append("description", description);
	formData.append("image", image);

	let response = await fetch(BUTCHGAMY + "/achievement", {
		method: "POST",
		body: formData,
	});

	return response.new; // true if this is a new achievement for the user
}

class Language {
	language;
	onSuccess;

	set locale(locale) {
		this.language = locale;
		localStorage.setItem("locale", locale);
		this.load(locale, this.onSuccess);
	}

	load(locale, onSuccess) {
		this.onSuccess = onSuccess;
		this.language = locale;
		fetch(`/assets/languages/${locale}.json`).then((request) => {
			if (!request.ok) throw new Error("Failed to load localizarion file");
			request.json().then((response) => {
				onSuccess instanceof Function && onSuccess(response);
			});
		});
	}
}

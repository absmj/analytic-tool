export class ChartJs {
	labels;
	el;
	type;
	data;
	pivot;
	htmlLegend;

	constructor(data, labels, type, htmlLegend, title = "Untitled") {
		this.data = data;
		this.title = title;
		this.type = type;
		this.labels = labels;
		// this.htmlLegend = htmlLegend;
	}

	chart(
		data = this.data,
		labels = this.labels,
		type = this.type,
		isTime = false
	) {
		let type_, dataset_;
		this.type = type;

		this.labels = labels;
		switch (this.type) {
			case "area":
				type_ = "line";
				break;

			case "donut":
				type_ = "doughnut";
				break;

			default:
				type_ = this.type;
		}

		switch (this.type) {
			case "area":
				dataset_ = data.map((s) => ({ ...s, fill: true }));
				break;

			case "tree":
				dataset_ = data.map((s) => ({ ...s, tree: s.data }));
				break;

			default:
				dataset_ = data;
		}

		// console.log(isTime);
		return {
			type: type_,
			total: data?.reduce((a, b) => a + b.total, 0),

			data: {
				// labels,
				datasets: dataset_,
			},
			options: {
				...this[type_ == "card" ? "line" : type_],
				scales: isTime ? this.timeFormatScale : null,
				plugins: {
					image: this.image,
					title: this.titlePlugin,
					legend: {
						display: true,
						onClick: legendOnclick,
					},
				},
				responsive: true,
				maintainAspectRatio: false,
			},
			plugins: [],
		};
	}

	get timeFormatScale() {
		return {
			x: {
				position: "bottom",
				type: "time",
				ticks: {
					autoSkip: true,
					autoSkipPadding: 50,
					maxRotation: 0,
					major: {
						enabled: true,
					},
				},
				time: {
					unit: "day",
					displayFormats: {
						day: "DD MMM",
					},
				},
			},
			y: {
				position: "right",
				ticks: {
					callback: (val, index, ticks) =>
						index === 0 || index === ticks.length - 1 ? null : val,
				},
			},
		};
	}

	get height() {
		return "auto";
		// return this.el.clientHeight
	}

	get width() {
		return "100%";
	}

	get series() {
		const rows = Object.keys(this.data.colValues);
		if (rows.length > 0) {
			const data = [];
			const dataMap = new Map();

			for (let i = 0; i < this.pivot.meta.vAmount; i++) {
				for (const rKey of rows) {
					const cObject = this.data.colValues[rKey];
					let colIndex = 0;
					for (const cKey of Object.keys(cObject)) {
						const vValue = cObject[cKey];
						data[colIndex + i * rows.length] = {
							name: this.pivot.meta["v" + i + "Name"] + " " + cKey,
							label: cKey,
							data: [
								...Object.keys(this.data.colValues).map(
									(v) =>
										Object.keys(this.data.colValues[v])
											.filter((a) => a == cKey)
											.map((b) => this.data.colValues[v][b])[0]
								),
							],
						};
						colIndex++;
					}
				}
			}
			return data;
		} else {
			return this.data.labels.map((d, k) => ({
				label: d,
				data: this.data.values[k],
			}));
		}
	}

	get area() {
		return {};
	}

	get image() {
		return {
			image: {
				emoji: null,
				position: null,
			},
		};
	}

	get titlePlugin() {
		return {
			display: true,
			text: `Chart.js Chart - ${this.type}`,
			font: {
				size: 16,
			},
		};
	}

	get bar() {
		return {
			plugins: {},
			responsive: true,
			scales: {
				x: {
					stacked: true,
				},
				y: {
					stacked: true,
				},
			},
		};
	}

	get line() {
		return {};
	}

	get radar() {
		return {};
	}

	get donut() {
		return {};
	}

	get pie() {
		return {};
	}

	get radialBar() {
		return {};
	}

	get gauge() {
		return {};
	}

	get treeMap() {
		return {};
	}

	get polarArea() {
		return {};
	}

	get dataLabels() {
		return {};
	}

	get zoomableTimeseries() {
		return {};
	}

	get lineChartAnnotations() {
		return {};
	}

	set height(h) {
		this.height = h;
	}
}

export function legendOnclick(e, li, l) {
	const id = e.chart.canvas.dataset.id;

	document
		?.getElementById(id)
		?.querySelector(`[data-label='${li.text}']`)
		?.click();
}

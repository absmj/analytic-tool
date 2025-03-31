const plugin = {
	id: "customCanvasBackgroundColor",
	beforeDraw: (chart, args, options) => {
		const { ctx } = chart;
		ctx.save();
		ctx.globalCompositeOperation = "destination-over";
		ctx.fillStyle = options.color || "#99ffff";
		ctx.fillRect(0, 0, chart.width, chart.height);
		ctx.restore();
	},
};

class ChartJs {
	labels;
	el;
	type;
	data;
	pivot;
	htmlLegend;

	constructor() {}

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

			case "bar-h":
				type_ = "bar";
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

		const options = {
			type: type_,
			total: data?.reduce((a, b) => a + b.total, 0),

			data: {
				labels,
				datasets: dataset_,
			},
			options: {
				...this[type_ == "card" ? "line" : type_],
				scales: isTime ? this.timeFormatScale : null,
				plugins: {
					customCanvasBackgroundColor: {
						color: "white",
					},
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
			plugins: [plugin],
		};

		if (this.type == "bar-h")
			options.options.indexAxis = this.type == "bar-h" ? "y" : null;

		return options;
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

function legendOnclick(e, li, l) {
	const id = e.chart.canvas.dataset.id;

	document
		?.getElementById(id)
		?.querySelector(`[data-label='${li.text}']`)
		?.click();
}

function makeOptionFieldChartJs(
	options,
	components = [],
	parentKey = null,
	index = 0
) {
	for (let i in options) {
		const inputId = (parentKey ? `${parentKey}.${i}` : i).replace(
			/^val\.|\.val/,
			""
		);
		if (options[i]?.typeof == "array" && Array.isArray(options[i].val)) {
			makeOptionFieldChartJs(
				options[i].val,
				components,
				inputId.replace(/\d+$/, index + 1)
			);
		} else if (
			options[i]?.hasOwnProperty("val") &&
			typeof options[i].val == "object"
		) {
			makeOptionFieldChartJs(options[i].val, components, inputId);
		} else if (typeof options[i] == "object") {
			makeOptionFieldChartJs(options[i], components, inputId);
		}

		if (options[i]?.hasOwnProperty("key")) {
			if (["key", "typeof"].includes(i)) continue;
			if (options[i]?.typeof == "array") {
				options[i].val?.forEach(
					(el, key) =>
						typeof el != "object" &&
						components.push(
							`
                    <div class="col-12">
                        <label class="form-label">${"Leybl #" + key}</label>
                        <input
                            name=${inputId + "." + key}
                            class="form-control"
                            value=${el}
                            type="text"
                        />
                    </div>
                `
						)
				);
			} else if (options[i]?.typeof == "boolean") {
				components.push(
					`
                    <div class="col-12">
                        <label class="form-label">${options[i].key}</label>
                        <input
                            name=${inputId}
                            class="form-control"
                            value=${options[i].val}
                            type="checkbox"
                        />
                    </div>
                `
				);
			} else if (options[i]?.typeof == "selectable") {
				components.push(
					`
                <div class="col-12">
                    <label class="form-label">${options[i].key}</label>
                    <select
                        name=${inputId}
                        class="form-select"
                        value=${options[i].val}>
                        <option value=""></option>
                        ${Object.keys(options[i].list)
													.map(
														(key, index) =>
															`<option key=${key} value=${key}>
															${options[i].list[key]}
														</option>`
													)
													.join("")}
                    </select>
                </div>`
				);
			} else if (options[i]?.typeof == "component") {
				components.push(options[i].component);
			} else if (
				options[i]?.typeof != "object" &&
				!inputId?.includes("component")
			) {
				components.push(
					`
                    <div class="col-12">
                        <label class="form-label">${options[i].key}</label>
                        <input
                            name=${inputId}
                            class="form-control"
                            value=${options[i].val}
                            max=${options[i]?.max || 100}
                            min=${options[i]?.min || 100}
                            type=${
															/size$/.test(i)
																? "range"
																: options[i]?.typeof || "text"
														}
                        />
                    </div>`
				);
			}
		}
	}

	return components;
}

class ChartJsInterface {
	options;
	dataset;
	type;

	line(data) {
		return {
			tension: {
				key: "Bézier əyrisi gərginliyi",
				typeof: "number",
				default: 0,
				description: "Bézier əyrisi gərginliyi (0 üçün Bézier əyrisi yoxdur).",
				val: data?.tension,
			},
			backgroundColor: {
				key: "Fon rəngi",
				typeof: "color",
				default: "Chart.defaults.backgroundColor",
				description: "Xətt doldurma rəngi.",
				val: data?.backgroundColor,
			},
			borderWidth: {
				key: "Xətt stroke eni",
				typeof: "number",
				default: 3,
				description: "Xətt stroke eni.",
				val: data?.borderWidth,
			},
			borderColor: {
				key: "Xətt stroke rəngi",
				typeof: "color",
				default: "Chart.defaults.borderColor",
				description: "Xətt stroke rəngi.",
				val: data?.borderColor,
			},
			borderCapStyle: {
				key: "Xətt cap üslubu",
				typeof: "selectable",
				list: {
					butt: "'butt'",
					round: "'round'",
					square: "'square'",
				},
				default: "'butt'",
				description: "Xətt cap üslubu. Daha ətraflı məlumat üçün MDN-ə baxın.",
				val: data?.borderCapStyle,
			},
			borderJoinStyle: {
				key: "Xətt birləşmə üslubu",
				typeof: "selectable",
				list: {
					round: "'round'",
					bevel: "'bevel'",
					miter: "'miter'",
				},
				default: "'miter'",
				description:
					"Xətt birləşmə üslubu. Daha ətraflı məlumat üçün MDN-ə baxın.",
				val: data?.borderJoinStyle,
			},
			capBezierPoints: {
				key: "Bézier idarəetmə nöqtələrinin məhdudlaşdırılması",
				typeof: "boolean",
				default: true,
				description:
					"Bézier idarəetmə nöqtələrinin chart içində qalmasını təmin edin, məhdudiyyət olmasınsa false seçin.",
				val: data?.capBezierPoints,
			},
			cubicInterpolationMode: {
				key: "Kub interpolasiya rejimi",
				typeof: "string",
				default: "'default'",
				description:
					"Tətbiq olunacaq interpolasiya rejimi. Daha ətraflı məlumat üçün baxın.",
				val: data?.cubicInterpolationMode,
			},
			fill: {
				key: "Xəttin alt hissəsinin doldurulması",
				typeof: "boolean|string",
				default: false,
				description:
					"Xəttin altını necə doldurmaq olar. Sahə qrafiklərinə baxın.",
				val: data?.fill,
			},
			stepped: {
				key: "Addımlı xətt göstərilməsi",
				typeof: "boolean",
				default: false,
				description:
					"Xətti addımlı xətt kimi göstərmək üçün true seçin (gərginlik nəzərə alınmayacaq).",
				val: data?.stepped,
			},
		};
	}

	bar(data) {
		return {
			backgroundColor: {
				key: "Barın fon rəngi",
				typeof: "color",
				default: "Chart.defaults.backgroundColor",
				description: "Barın doldurma rəngi.",
				val: data?.backgroundColor,
			},
			borderWidth: {
				key: "Barın stroke eni",
				typeof: "number",
				default: 0,
				description: "Barın stroke eni.",
				val: data?.borderWidth,
			},
			borderColor: {
				key: "Barın stroke rəngi",
				typeof: "color",
				default: "Chart.defaults.borderColor",
				description: "Barın stroke rəngi.",
				val: data?.borderColor,
			},
			borderSkipped: {
				key: "Keçilən sərhəd",
				typeof: "string",
				default: "'start'",
				description:
					"Keçilən (daxil edilməyən) sərhəd: 'start', 'end', 'middle', 'bottom', 'left', 'top', 'right' və ya false.",
				val: data?.borderSkipped,
			},
			borderRadius: {
				key: "Bar sərhəd radiusu",
				typeof: "number|object",
				default: 0,
				description: "Bar sərhəd radiusu (piksel olaraq).",
				val: data?.borderRadius,
			},
			inflateAmount: {
				key: "Barın şişirdilməsi miqdarı",
				typeof: "number|'auto'",
				default: "'auto'",
				description:
					"Çizim zamanı bar düzbucaqlarını şişirtmək üçün istifadə olunan piksel miqdarı.",
				val: data?.inflateAmount,
			},
			pointStyle: this.pointStyle(data?.pointStyle),
		};
	}

	arc(data) {
		return {
			angle: {
				key: "Yalnız polar üçün bucaq",
				typeof: "number",
				default: "circumference / (arc count)",
				description: "Yay örtəcək bucaq.",
				val: data?.angle,
			},
			backgroundColor: {
				key: "Yay doldurma rəngi",
				typeof: "color",
				default: "Chart.defaults.backgroundColor",
				description: "Yay doldurma rəngi.",
				val: data?.backgroundColor,
			},
			borderAlign: {
				key: "Yay stroke hizalanması",
				typeof: "'center'|'inner'",
				default: "'center'",
				description: "Yay stroke hizalanması.",
				val: data?.borderAlign,
			},
			borderColor: {
				key: "Yay stroke rəngi",
				typeof: "color",
				default: "'#fff'",
				description: "Yay stroke rəngi.",
				val: data?.borderColor,
			},
			borderDash: {
				key: "Yay xətti tire",
				typeof: "number[]",
				default: "[]",
				description: "Yay xətti tire. Daha ətraflı məlumat üçün MDN-ə baxın.",
				val: data?.borderDash,
			},
			borderDashOffset: {
				key: "Yay xətti tire ofseti",
				typeof: "number",
				default: 0.0,
				description:
					"Yay xətti tire ofseti. Daha ətraflı məlumat üçün MDN-ə baxın.",
				val: data?.borderDashOffset,
			},
			borderJoinStyle: {
				key: "Xətt birləşmə üslubu",
				typeof: "'round'|'bevel'|'miter'",
				default: "'bevel'|'round'",
				description:
					"Xətt birləşmə üslubu. BorderAlign 'inner' olduqda default 'round' olur.",
				val: data?.borderJoinStyle,
			},
			borderWidth: {
				key: "Yay stroke eni",
				typeof: "number",
				default: 2,
				description: "Yay stroke eni.",
				val: data?.borderWidth,
			},
			circular: {
				key: "Dairəvi",
				typeof: "boolean",
				default: true,
				description:
					"Default olaraq yay əyri olur. circular: false olduqda yay düz olur.",
				val: data?.circular,
			},
		};
	}

	card(data) {
		return {
			backgroundColor: this.color(data?.backgroundColor),
		};
	}

	position(data) {
		return {
			key: "Mövqe",
			typeof: "selectable",
			list: {
				top: "Yuxarı",
				bottom: "Aşağı",
				left: "Sol",
				right: "Sağ",
				chartArea: "Çart daxilində",
			},
			val: data,
		};
	}
	align(data) {
		return {
			key: "Yerləşmə",
			typeof: "selectable",
			list: {
				start: "başlanğıc",
				center: "mərkəz",
				end: "son",
			},
			val: data,
		};
	}
	pointStyle(data) {
		return {
			key: "Nöqtə üslubu",
			typeof: "selectable",
			list: {
				circle: "dairə",
				cross: "xaç",
				crossRot: "dönmüş xaç",
				dash: "tire",
				line: "xətt",
				rect: "düzbucaqlı",
				rectRounded: "yuvarlaqlaşdırılmış düzbucaqlı",
				rectRot: "dönmüş düzbucaqlı",
				star: "ulduz",
				triangle: "üçbucaq",
				false: "heç biri",
			},
			val: data,
		};
	}
	border(data) {
		return {
			display: this.display(data?.display),
		};
	}
	display(data) {
		return { key: "Göstər", typeof: "boolean", val: data || false };
	}
	grid(data) {
		return {
			display: this.display(data?.display),
			drawOnChartArea: {
				key: "Çartın üzərindən çək",
				typeof: "boolean",
				val: data?.drawOnChartArea,
			},
			drawTicks: {
				key: "Nöqtələri göstər",
				typeof: "boolean",
				val: data?.drawTicks,
			},
			color: this.color(data?.color),
		};
	}
	color(color) {
		return { key: "Rəng", typeof: "color", val: color || "#000000" };
	}
	padding(data) {
		return {
			top: {
				key: "Yuxarı",
				typeof: "number",
				val: data?.top,
			},
			left: {
				key: "Sol",
				typeof: "number",
				val: data?.left,
			},
			right: {
				key: "Sağ",
				typeof: "number",
				val: data?.right,
			},
			bottom: {
				key: "Aşağı",
				typeof: "number",
				val: data?.bottom,
			},
		};
	}
	font(data) {
		return {
			family: {
				key: "Şrift",
				typeof: "selectable",
				list: {
					["'Helvetica Neue'"]: "Helvetica Neue",
					["'Helvetica'"]: "Helvetica",
					["sans-serif"]: "sans-serif",
				},
				val: data?.family,
			},
			size: {
				key: "Ölçü",
				typeof: "number",
				val: data?.size,
			},
			style: {
				key: "Üslubu",
				typeof: "selectable",
				list: {
					normal: "normal",
					italic: "italic",
					oblique: "oblique",
					initial: "initial",
					inherit: "inherit",
				},
				val: data?.weight,
			},
			weight: {
				key: "Qalınlıq",
				typeof: "selectable",
				list: {
					normal: "normal",
					bolder: "bolder",
					lighter: "lighter",
				},
				val: data?.weight,
			},
			lineHeight: {
				key: "Sətir hündürlüyü",
				typeof: "number",
				val: data?.lineHeight,
			},
		};
	}
	title(data) {
		return {
			display: this.display(data?.display),
			text: {
				key: "Başlıq",
				typeof: "string",
				val: data?.text,
			},
			color: this.color(data?.color),
			font: this.font(data?.font),
			padding: this.padding(data?.padding),
		};
	}
	get data() {
		return {
			labels: {
				key: "Leyblar",
				typeof: "array",
				val: this.dataset?.labels,
			},
			datasets: {
				key: "Datasetlər",
				typeof: "array",
				val: this.dataset?.map((dataset) => ({
					type: {
						key: "Tipi",
						typeof: "selectable",
						list: {
							line: "line",
							area: "area",
							bar: "bar",
							donut: "donut",
							pie: "pie",
							scatter: "scatter",
							gauge: "gauge",
							treemap: "treemap",
							radar: "radar",
						},
						val: dataset.type,
					},
					fill: { key: "Doldur", typeof: "boolean", val: dataset?.fill },
					pointRadius: {
						key: "Nöqtə radiusu",
						typeof: "number",
						val: dataset?.pointRadius,
					},
					pointStyle: this.pointStyle(dataset?.pointStyle),
					label: { key: "Leybl", typeof: "text", val: dataset.label },
					backgroundColor: {
						key: "Arxa fon rəngi",
						typeof: "color",
						val: dataset.backgroundColor,
					},
					borderColor: {
						key: "Çərçivə rəngi",
						typeof: "color",
						val: dataset.borderColor,
					},
				})),
			},
		};
	}
	get scale() {
		const maxMin = (data) => {
			return {
				min: {
					key: "Minimum",
					typeof: "number",
					val: data?.min,
				},
				max: {
					key: "Maksimum",
					typeof: "number",
					val: data?.max,
				},
			};
		};
		return {
			x: {
				key: "x oxu",
				typeof: "object",
				val: {
					...maxMin(this.options?.scale?.x),
					...this.border(this.options?.scale?.x),
					...this.grid(this.options?.scale?.x),
					...this.title(this.options?.scale?.x),
				},
			},
			y: {
				key: "y oxu",
				typeof: "object",
				val: {
					...maxMin(this.options?.scale?.y),
					...this.border(this.options?.scale?.y),
					...this.grid(this.options?.scale?.y),
					...this.title(this.options?.scale?.y),
				},
			},
		};
	}
	get legend() {
		return {
			title: this.title(this.options?.plugins?.legend?.title),
			position: this.position(this.options?.plugins?.legend?.position),
			align: this.align(this.options?.plugins?.legend?.align),
			maxHeight: {
				key: "Maks hündürlük",
				typeof: "number",
				data: this.options?.plugins?.legend?.maxHeight,
			},
			maxWidth: {
				key: "Maks en",
				typeof: "number",
				data: this.options?.plugins?.legend?.maxHeight,
			},
			fullSize: {
				key: "Tam ölçü",
				typeof: "boolean",
				data: this.options?.plugins?.legend?.fullSize,
			},
			reverse: {
				key: "Tərsinə",
				typeof: "boolean",
				data: this.options?.plugins?.legend?.reverse,
			},
		};
	}
	get subtitle() {
		return {
			...this.title(this.options?.plugins?.subtitle),
		};
	}

	get image() {
		return {
			emoji: {
				key: "Arxa fon şəkili (emoji)",
				typeof: "component",
				render: "string",
				component: createElement(
					"div",
					{
						title: "Arxa fon şəklini emojidən seç",
						width: "100%",
					},
					createElement("input", {
						id: "selectEmoji",
						name: "options.plugins.image.emoji",
						style: {
							display: "none",
						},
					}),
					createElement(EmojiPicker, {
						width: "100%",
						onEmojiClick: (emoji, event) => {
							const el =
								event.target.ownerDocument.playgroundForm.querySelector(
									"#selectEmoji"
								);
							const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
								window.HTMLInputElement.prototype,
								"value"
							).set;
							nativeInputValueSetter.call(el, emoji.emoji);

							el.dispatchEvent(new Event("input", { bubbles: true }));
						},
					})
				),
				data: this.options?.plugins?.image?.emoji,
			},
			size: {
				key: "Ölçü",
				typeof: "range",
				max: 30,
				min: 2,
				data: this.options?.plugins?.image?.size,
			},
			size: {
				key: "Şəffaflıq",
				typeof: "range",
				max: 1,
				min: 0,
				data: this.options?.plugins?.image?.opacity,
			},
			top: {
				key: "Yuxarı yerləşmə",
				typeof: "range",
				max: 100,
				min: 1,
				data: this.options?.plugins?.image?.top,
			},
			left: {
				key: "Sol yerləşmə",
				typeof: "range",
				max: 100,
				min: 1,
				data: this.options?.plugins?.image?.left,
			},
		};
	}

	// playgrounds(type) {
	//   return {
	//     [`options.elements.${type}`]: this?.[type] ? Settings : null,
	//     "options.plugins.title": TitleOutlined,
	//     // "options.plugins.subtitle": SubtitlesOutlined,
	//     "options.plugins.image": Image,
	//     "options.plugins.legend": LegendToggleOutlined,
	//     data: DataObject,
	//     "options.scale": ScaleOutlined,
	//   };
	// }

	render(chart) {
		this.options = chart.options.options;
		this.dataset = chart.dataset;
		this.type = chart.type;

		switch (this.type) {
			case "card":
				return {
					options: {
						elements: {
							card: this.card(this.options?.elements?.card),
						},
						plugins: {
							title: this.title(this.options?.plugins?.title),
							legend: this.legend,
							subtitle: this.subtitle,
							image: this.image,
						},
					},
				};

			default:
				return {
					options: {
						elements: {
							[this.type]: this?.[this.type]
								? this[this.type](this.options?.elements?.[this.type])
								: null,
						},
						plugins: {
							title: this.title(this.options?.plugins?.title),
							legend: this.legend,
							subtitle: this.subtitle,
						},
						scale: this.scale,
					},

					data: this.data,
				};
		}
	}
}

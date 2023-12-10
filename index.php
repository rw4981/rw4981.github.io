<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<title>client</title>
		<style>
body {
	touch-action: none;
	margin: 0;
	border: 0 none;
	padding: 0;
	text-align: center;
	background-color: black;
}

#canvas {
	display: block;
	margin: 0;
	color: white;
}

#canvas:focus {
	outline: none;
}

.godot {
	font-family: 'Noto Sans', 'Droid Sans', Arial, sans-serif;
	color: #e0e0e0;
	background-color: #3b3943;
	background-image: linear-gradient(to bottom, #403e48, #35333c);
	border: 1px solid #45434e;
	box-shadow: 0 0 1px 1px #2f2d35;
}

/* Status display */

#status {
	position: absolute;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	/* don't consume click events - make children visible explicitly */
	visibility: hidden;
}

#status-progress {
	width: 366px;
	height: 7px;
	background-color: #38363A;
	border: 1px solid #444246;
	padding: 1px;
	box-shadow: 0 0 2px 1px #1B1C22;
	border-radius: 2px;
	visibility: visible;
}

@media only screen and (orientation:portrait) {
	#status-progress {
		width: 61.8%;
	}
}

#status-progress-inner {
	height: 100%;
	width: 0;
	box-sizing: border-box;
	transition: width 0.5s linear;
	background-color: #202020;
	border: 1px solid #222223;
	box-shadow: 0 0 1px 1px #27282E;
	border-radius: 3px;
}

#status-indeterminate {
	height: 42px;
	visibility: visible;
	position: relative;
}

#status-indeterminate > div {
	width: 4.5px;
	height: 0;
	border-style: solid;
	border-width: 9px 3px 0 3px;
	border-color: #2b2b2b transparent transparent transparent;
	transform-origin: center 21px;
	position: absolute;
}

#status-indeterminate > div:nth-child(1) { transform: rotate( 22.5deg); }
#status-indeterminate > div:nth-child(2) { transform: rotate( 67.5deg); }
#status-indeterminate > div:nth-child(3) { transform: rotate(112.5deg); }
#status-indeterminate > div:nth-child(4) { transform: rotate(157.5deg); }
#status-indeterminate > div:nth-child(5) { transform: rotate(202.5deg); }
#status-indeterminate > div:nth-child(6) { transform: rotate(247.5deg); }
#status-indeterminate > div:nth-child(7) { transform: rotate(292.5deg); }
#status-indeterminate > div:nth-child(8) { transform: rotate(337.5deg); }

#status-notice {
	margin: 0 100px;
	line-height: 1.3;
	visibility: visible;
	padding: 4px 6px;
	visibility: visible;
}
		</style>
		<link id='-gd-engine-icon' rel='icon' type='image/png' href='index.icon.png' />
<link rel='apple-touch-icon' href='index.apple-touch-icon.png'/>

	</head>
	<body>
		<canvas id="canvas">
			HTML5 canvas appears to be unsupported in the current browser.<br >
			Please try updating or use a different browser.
		</canvas>
		<div id="status">
			<div id="status-progress" style="display: none;" oncontextmenu="event.preventDefault();">
				<div id ="status-progress-inner"></div>
			</div>
			<div id="status-indeterminate" style="display: none;" oncontextmenu="event.preventDefault();">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
			<div id="status-notice" class="godot" style="display: none;"></div>
		</div>

		<script src="index.js"></script>
		<script>
const GODOT_CONFIG = {"args":[],"canvasResizePolicy":2,"executable":"index","experimentalVK":true,"fileSizes":{"index.pck":18849744,"index.wasm":48593119},"focusCanvas":true,"gdextensionLibs":[]};
const engine = new Engine(GODOT_CONFIG);

(function () {
	const INDETERMINATE_STATUS_STEP_MS = 100;
	const statusProgress = document.getElementById('status-progress');
	const statusProgressInner = document.getElementById('status-progress-inner');
	const statusIndeterminate = document.getElementById('status-indeterminate');
	const statusNotice = document.getElementById('status-notice');

	let initializing = true;
	let statusMode = 'hidden';

	let animationCallbacks = [];
	function animate(time) {
		animationCallbacks.forEach((callback) => callback(time));
		requestAnimationFrame(animate);
	}
	requestAnimationFrame(animate);

	function animateStatusIndeterminate(ms) {
		const i = Math.floor((ms / INDETERMINATE_STATUS_STEP_MS) % 8);
		if (statusIndeterminate.children[i].style.borderTopColor === '') {
			Array.prototype.slice.call(statusIndeterminate.children).forEach((child) => {
				child.style.borderTopColor = '';
			});
			statusIndeterminate.children[i].style.borderTopColor = '#dfdfdf';
		}
	}

	function setStatusMode(mode) {
		if (statusMode === mode || !initializing) {
			return;
		}
		[statusProgress, statusIndeterminate, statusNotice].forEach((elem) => {
			elem.style.display = 'none';
		});
		animationCallbacks = animationCallbacks.filter(function (value) {
			return (value !== animateStatusIndeterminate);
		});
		switch (mode) {
		case 'progress':
			statusProgress.style.display = 'block';
			break;
		case 'indeterminate':
			statusIndeterminate.style.display = 'block';
			animationCallbacks.push(animateStatusIndeterminate);
			break;
		case 'notice':
			statusNotice.style.display = 'block';
			break;
		case 'hidden':
			break;
		default:
			throw new Error('Invalid status mode');
		}
		statusMode = mode;
	}

	function setStatusNotice(text) {
		while (statusNotice.lastChild) {
			statusNotice.removeChild(statusNotice.lastChild);
		}
		const lines = text.split('\n');
		lines.forEach((line) => {
			statusNotice.appendChild(document.createTextNode(line));
			statusNotice.appendChild(document.createElement('br'));
		});
	}

	function displayFailureNotice(err) {
		const msg = err.message || err;
		console.error(msg);
		setStatusNotice(msg);
		setStatusMode('notice');
		initializing = false;
	}

	const missing = Engine.getMissingFeatures();
	if (missing.length !== 0) {
		const missingMsg = 'Error\nThe following features required to run Godot projects on the Web are missing:\n';
		displayFailureNotice(missingMsg + missing.join('\n'));
	} else {
		setStatusMode('indeterminate');
		engine.startGame({
			'onProgress': function (current, total) {
				if (total > 0) {
					statusProgressInner.style.width = `${(current / total) * 100}%`;
					setStatusMode('progress');
					if (current === total) {
						// wait for progress bar animation
						setTimeout(() => {
							setStatusMode('indeterminate');
						}, 500);
					}
				} else {
					setStatusMode('indeterminate');
				}
			},
		}).then(() => {
			setStatusMode('hidden');
			initializing = false;
		}, displayFailureNotice);
	}
}());
		</script>
	</body>
</html>


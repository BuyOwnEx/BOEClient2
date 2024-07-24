export class ClassicalNoise {
	constructor(r = Math) {
		this.grad3 = [[1, 1, 0], [-1, 1, 0], [1, -1, 0], [-1, -1, 0], [1, 0, 1], [-1, 0, 1], [1, 0, -1], [-1, 0, -1], [0, 1, 1], [0, -1, 1], [0, 1, -1], [0, -1, -1]];
		this.p = [];
		for (let i = 0; i < 256; i++) {
			this.p[i] = Math.floor(r.random() * 256);
		}
		this.perm = [];
		for (let i = 0; i < 512; i++) {
			this.perm[i] = this.p[i & 255];
		}
		this.isStarted = false;
	}

	dot(g, x, y, z) {
		return g[0] * x + g[1] * y + g[2] * z;
	}

	mix(a, b, t) {
		return (1.0 - t) * a + t * b;
	}

	fade(t) {
		return t * t * t * (t * (t * 6.0 - 15.0) + 10.0);
	}

	noise(x, y, z) {
		let X = Math.floor(x);
		let Y = Math.floor(y);
		let Z = Math.floor(z);

		x = x - X;
		y = y - Y;
		z = z - Z;

		X = X & 255;
		Y = Y & 255;
		Z = Z & 255;

		let gi000 = this.perm[X + this.perm[Y + this.perm[Z]]] % 12;
		let gi001 = this.perm[X + this.perm[Y + this.perm[Z + 1]]] % 12;
		let gi010 = this.perm[X + this.perm[Y + 1 + this.perm[Z]]] % 12;
		let gi011 = this.perm[X + this.perm[Y + 1 + this.perm[Z + 1]]] % 12;
		let gi100 = this.perm[X + 1 + this.perm[Y + this.perm[Z]]] % 12;
		let gi101 = this.perm[X + 1 + this.perm[Y + this.perm[Z + 1]]] % 12;
		let gi110 = this.perm[X + 1 + this.perm[Y + 1 + this.perm[Z]]] % 12;
		let gi111 = this.perm[X + 1 + this.perm[Y + 1 + this.perm[Z + 1]]] % 12;

		let n000 = this.dot(this.grad3[gi000], x, y, z);
		let n100 = this.dot(this.grad3[gi100], x - 1, y, z);
		let n010 = this.dot(this.grad3[gi010], x, y - 1, z);
		let n110 = this.dot(this.grad3[gi110], x - 1, y - 1, z);
		let n001 = this.dot(this.grad3[gi001], x, y, z - 1);
		let n101 = this.dot(this.grad3[gi101], x - 1, y, z - 1);
		let n011 = this.dot(this.grad3[gi011], x, y - 1, z - 1);
		let n111 = this.dot(this.grad3[gi111], x - 1, y - 1, z - 1);

		let u = this.fade(x);
		let v = this.fade(y);
		let w = this.fade(z);

		let nx00 = this.mix(n000, n100, u);
		let nx01 = this.mix(n001, n101, u);
		let nx10 = this.mix(n010, n110, u);
		let nx11 = this.mix(n011, n111, u);

		let nxy0 = this.mix(nx00, nx10, v);
		let nxy1 = this.mix(nx01, nx11, v);

		return this.mix(nxy0, nxy1, w);
	}

	stop() {
		this.isStarted = false;
	}

	start(canvas) {
		this.isStarted = true;
		// let canvas = document.getElementById(id);

		if (canvas) {
			let ctx = canvas.getContext('2d'),
				perlin = new ClassicalNoise(),
				variation = .001,
				amp = 800,
				maxLines = 52,
				variators = [],
				canvasWidth,
				canvasHeight,
				startY;

			for (let i = 0, u = 0; i < maxLines; i++, u += .02) {
				variators[i] = u;
			}

			const draw = () => {
				ctx.shadowColor = 'rgba(195,204,224)';
				ctx.shadowBlur = 0;

				for (let i = 0; i <= maxLines; i++) {
					ctx.beginPath();
					ctx.moveTo(0, startY);
					let y = 0;
					for (let x = 0; x <= canvasWidth; x++) {
						y = perlin.noise(x * variation + variators[i], x * variation, 0);
						ctx.lineTo(x, startY + amp * y);
					}
					let alpha = Math.min(Math.abs(y), .7) + .1;
					ctx.strokeStyle = 'rgba(3,164,194,' + alpha + ')';
					ctx.stroke();
					ctx.closePath();
					variators[i] += .002;
				}
			};

			const resizeCanvas = () => {
				canvasWidth = document.documentElement.clientWidth;
				canvasHeight = document.documentElement.clientHeight;
				canvas.setAttribute('width', canvasWidth);
				canvas.setAttribute('height', canvasHeight);
				startY = canvasHeight / 2;
			};

			const animate = () => {
				if (this.isStarted) {
					ctx.clearRect(0, 0, canvasWidth, canvasHeight);
					draw();
					requestAnimationFrame(animate);
				}
			};

			(() => {
				resizeCanvas();
				animate();
				window.addEventListener('resize', resizeCanvas);
			})();
		}
	}
}

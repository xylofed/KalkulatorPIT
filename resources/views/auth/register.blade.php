@extends('layouts.app')

@section('content')
<style>
    /* Ten sam styl co dla logowania, ale na wszelki wypadek dopisuję */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;
        color: #e0e7ff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 20px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .card {
        background: rgba(38, 55, 112, 0.85);
        border: none;
        border-radius: 16px;
        box-shadow: 0 0 25px rgba(37, 117, 252, 0.7);
        width: 100%;
        max-width: 600px;
        color: #e0e7ff;
        padding: 30px 40px;
    }

    .card-header {
        background: transparent;
        border-bottom: none;
        font-size: 2rem;
        font-weight: 700;
        color: #ffdd59;
        text-align: center;
        text-shadow: 0 0 12px rgba(255, 221, 89, 0.7);
        padding-bottom: 20px;
        border-radius: 16px 16px 0 0;
    }

    label.col-form-label {
        color: #ffdd59;
        font-weight: 600;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.15);
        border: 1.5px solid rgba(255, 221, 89, 0.85);
        border-radius: 8px;
        color: #ffdd59;
        padding: 10px 12px;
        font-size: 1rem;
        transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
    }

    .form-control::placeholder {
        color: #ffe680;
        opacity: 0.7;
    }

    .form-control:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.25);
        border-color: #ffdd59;
        color: #222;
    }

    .is-invalid {
        border-color: #ff6b6b !important;
        background-color: rgba(255, 107, 107, 0.15);
        color: #ff6b6b !important;
    }

    .invalid-feedback {
        color: #ff6b6b;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .btn-primary {
        background-color: #ffdd59;
        border: none;
        color: #222;
        font-weight: 700;
        width: 100%;
        padding: 12px;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(255, 221, 89, 0.6);
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #f5c700;
        color: #111;
        box-shadow: 0 6px 14px rgba(245, 199, 0, 0.8);
    }

    @media (max-width: 768px) {
        .card {
            max-width: 100%;
            padding: 20px;
        }

        .card-header {
            font-size: 1.5rem;
        }
    }

#background-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 1;
        mix-blend-mode: screen;
    }
</style>

<canvas id="background-canvas"></canvas>
<div class="login-container">
    <div class="card">
        <div class="card-header">{{ __('Register') }}</div>

        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input id="name" type="text"
                        class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Wpisz swoje imię">

                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email" placeholder="Wpisz swój email">

                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password"
                        required autocomplete="new-password" placeholder="Wpisz hasło">

                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control"
                        name="password_confirmation" required autocomplete="new-password" placeholder="Potwierdź hasło">
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const canvas = document.getElementById('background-canvas');
    const ctx = canvas.getContext('2d');

    let width, height, points;
    const POINT_COUNT = 120;
    let mouse = { x: null, y: null };

    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width * devicePixelRatio;
        canvas.height = height * devicePixelRatio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(devicePixelRatio, devicePixelRatio);
    }

    function distance(p1, p2) {
        return Math.sqrt((p1.x - p2.x) ** 2 + (p1.y - p2.y) ** 2);
    }

    function findNearestNeighbors(point, allPoints, n = 4) {
        return allPoints
            .filter(p => p !== point)
            .map(p => ({ point: p, dist: distance(point, p) }))
            .sort((a, b) => a.dist - b.dist)
            .slice(0, n)
            .map(item => item.point);
    }

    function init() {
        resize();
        points = [];

        for (let i = 0; i < POINT_COUNT; i++) {
            points.push({
                x: Math.random() * width,
                y: Math.random() * height,
                originX: 0,
                originY: 0,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
            });
        }

        points.forEach(p => {
            p.originX = p.x;
            p.originY = p.y;
        });
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        points.forEach(p => {
            const neighbors = findNearestNeighbors(p, points, 4);

            neighbors.forEach(nbr => {
                const dist = distance(p, nbr);
                if (dist < 180) {
                    let opacity = 1 - dist / 180;

                    if (mouse.x !== null) {
                        const midX = (p.x + nbr.x) / 2;
                        const midY = (p.y + nbr.y) / 2;
                        const mouseDist = Math.sqrt((mouse.x - midX) ** 2 + (mouse.y - midY) ** 2);
                        if (mouseDist < 130) {
                            opacity = Math.min(1, opacity + (130 - mouseDist) / 130);
                        }
                    }

                    ctx.strokeStyle = `rgba(255, 221, 89, ${opacity * 0.6})`;
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(nbr.x, nbr.y);
                    ctx.stroke();
                }
            });

            if (mouse.x !== null) {
                const distToMouse = distance(p, mouse);
                if (distToMouse < 130) {
                    let opacity = 1 - distToMouse / 130;
                    ctx.strokeStyle = `rgba(255, 221, 89, ${opacity})`;
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.stroke();
                }
            }
        });

        points.forEach(p => {
            let radius = 2.5;
            let opacity = 0.4;

            if (mouse.x !== null) {
                const d = distance(p, mouse);
                if (d < 100) {
                    radius = 5;
                    opacity = 1;
                }
            }

            ctx.beginPath();
            ctx.arc(p.x, p.y, radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 221, 89, ${opacity})`;
            ctx.fill();
        });
    }

    function animate() {
        points.forEach(p => {
            p.vx += (Math.random() - 0.5) * 0.1;
            p.vy += (Math.random() - 0.5) * 0.1;

            p.x += p.vx;
            p.y += p.vy;

            p.vx += (p.originX - p.x) * 0.002;
            p.vy += (p.originY - p.y) * 0.002;

            p.vx *= 0.98;
            p.vy *= 0.98;

            if (p.x < 0) { p.x = 0; p.vx *= -0.7; }
            if (p.x > width) { p.x = width; p.vx *= -0.7; }
            if (p.y < 0) { p.y = 0; p.vy *= -0.7; }
            if (p.y > height) { p.y = height; p.vy *= -0.7; }
        });

        draw();
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => {
        resize();
        init();
    });

    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });

    window.addEventListener('mouseout', () => {
        mouse.x = null;
        mouse.y = null;
    });

    init();
    animate();
})();
</script>

@endsection

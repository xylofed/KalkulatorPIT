@extends('layouts.app')

@section('content')
<style>
    body, html {
        height: 100%;
        margin: 0;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #e0e7ff;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Wrapper, który wyśrodkuje zawartość i odsunię ją od navbaru */
    .content-wrapper {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 80px; /* Dostosuj do wysokości navbaru */
        padding-bottom: 40px;
        box-sizing: border-box;
        position: relative;
        z-index: 10;
        width: 100%;
    }

    .container.py-5 {
        max-width: 960px;
        background: rgba(38, 55, 112, 0.85);
        border-radius: 16px;
        box-shadow: 0 0 25px rgba(37, 117, 252, 0.7);
        padding: 40px 30px;
    }

    h1.display-4 {
        color: #ffdd59;
        text-shadow: 0 0 12px rgba(255, 221, 89, 0.7);
    }

    p.lead {
        font-size: 1.25rem;
        color: #d3d9ffcc;
    }

    .btn-primary {
        background-color: #ffdd59;
        border: none;
        color: #222;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(255, 221, 89, 0.6);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #f5c700;
        color: #111;
        box-shadow: 0 6px 14px rgba(245, 199, 0, 0.8);
    }

    .btn-outline-primary {
        color: #ffdd59;
        border-color: #ffdd59;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #ffdd59;
        color: #222;
    }

    .p-4.border.rounded.shadow-sm {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        color: #e0e7ff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: default;
    }

    .p-4.border.rounded.shadow-sm:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(255, 221, 89, 0.5);
    }

    h4.mb-3 {
        color: #ffdd59;
        text-shadow: 0 0 8px rgba(255, 221, 89, 0.6);
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

<div class="content-wrapper">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">Witaj w Kalkulatorze PIT</h1>
            <p class="lead">Szybko, łatwo i bez logowania – oblicz swój podatek dochodowy już teraz!</p>
            <a href="{{ route('pit-calculator') }}" class="btn btn-primary btn-lg mt-3">
                Oblicz podatek
            </a>
        </div>

        <div class="row text-center mb-5">
            <div class="col-md-4">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4 class="mb-3">Prosty interfejs</h4>
                    <p>Intuicyjny i przejrzysty formularz obliczeń – nawet bez wiedzy podatkowej.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4 class="mb-3">Aktualne przepisy</h4>
                    <p>Uwzględniamy najnowsze zmiany w prawie podatkowym w Polsce.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 border rounded shadow-sm h-100">
                    <h4 class="mb-3">Darmowe demo</h4>
                    <p>Wypróbuj kalkulator bez konieczności zakładania konta.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('pit-calculator.demo') }}" class="btn btn-outline-primary btn-lg px-5">
                Wypróbuj pełny kalkulator PIT (demo)
            </a>
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

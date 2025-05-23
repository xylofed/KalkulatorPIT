@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        min-height: 100vh;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #e0e7ffcc;
    }

    .container.py-4 {
        max-width: 960px;
        margin-top: 30px;
        margin-bottom: 50px;
        color: #e0e7ffcc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h1.mb-4.text-center {
        color: #ffdd59;
        text-shadow: 0 0 10px rgba(255, 221, 89, 0.6);
        font-weight: 700;
    }

    .alert-info {
        background-color: rgba(38, 55, 112, 0.7);
        border-color: rgba(38, 55, 112, 0.8);
        color: #d3d9ffcc;
        font-size: 1.15rem;
        text-align: center;
        box-shadow: 0 0 15px rgba(37, 117, 252, 0.4);
        border-radius: 12px;
        padding: 20px;
        user-select: none;
    }

    .card {
        background: rgba(38, 55, 112, 1);
        border: 1px solid rgba(255, 221, 89, 0.2);
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(255, 221, 89, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #d3d9ffcc;
        font-weight: 500;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(255, 221, 89, 0.35);
    }

    .card-body {
        padding: 1.25rem 1.5rem;
    }

    .card-title {
        color: #ffdd59;
        font-weight: 700;
        text-shadow: 0 0 8px rgba(255, 221, 89, 0.7);
        margin-bottom: 1rem;
    }

    .card-text {
        color: #d3d9ffcc;
        font-size: 1rem;
        margin-bottom: 0.6rem;
    }

    .card-footer {
        background: rgba(38, 55, 112, 0.5);
        border-top: 1px solid rgba(255, 221, 89, 0.15);
        padding: 0.75rem 1.5rem;
        border-radius: 0 0 14px 14px;
    }

    .btn-outline-warning {
        color: #ffdd59;
        border-color: #ffdd59;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-warning:hover {
        background-color: #ffdd59;
        color: #222;
        box-shadow: 0 6px 14px rgba(255, 221, 89, 0.5);
    }

    .btn-outline-danger {
        color: #ff6b6b;
        border-color: #ff6b6b;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-danger:hover {
        background-color: #ff6b6b;
        color: #222;
        box-shadow: 0 6px 14px rgba(255, 107, 107, 0.5);
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

    /* Responsywność */
    @media (max-width: 575.98px) {
        .card-text {
            font-size: 0.9rem;
        }
    }
</style>

<canvas id="background-canvas"></canvas>

<div class="container py-4">
    <h1 class="mb-4 text-center">Moje kalkulacje PIT</h1>

    @if ($taxCalculations->isEmpty())
        <div class="alert alert-info">
            Brak zapisanych kalkulacji.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($taxCalculations as $taxCalculation)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Kalkulacja #{{ $loop->iteration }}</h5>
                            <p class="card-text mb-1"><strong>Dochód:</strong> {{ number_format($taxCalculation->income, 2, ',', ' ') }} zł</p>
                            <p class="card-text"><strong>Kwota podatku:</strong> {{ number_format($taxCalculation->tax_amount, 2, ',', ' ') }} zł</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('tax-calculations.edit', $taxCalculation->id) }}" class="btn btn-sm btn-outline-warning">
                                Edytuj
                            </a>
                            <form action="{{ route('tax-calculations.destroy', $taxCalculation->id) }}" method="POST"
                                onsubmit="return confirm('Czy na pewno chcesz usunąć tę kalkulację?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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

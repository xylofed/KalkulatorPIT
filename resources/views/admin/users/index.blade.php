@extends('layouts.app')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #f0f4ff;
        overflow-x: hidden;
    }

    .container {
        max-width: 960px;
        margin: 40px auto;
        background: rgba(38, 55, 112, 0.95);
        padding: 30px 20px;
        border-radius: 16px;
        box-shadow: 0 0 40px rgba(37, 117, 252, 0.85);
        color: #f0f4ff;
        position: relative;
        z-index: 2;
    }

    h2 {
        color: #ffdd59;
        text-shadow: 0 0 12px rgba(255, 221, 89, 0.7);
        font-size: 1.6rem;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .table {
        background: rgba(38, 55, 112, 0.4);
        backdrop-filter: blur(8px);
        border-radius: 8px;
        color: #e0e7ff;
        width: 100%;
        min-width: 600px;
    }

    .table th {
        background-color: rgba(38, 55, 112, 0.7);
        color: #ffdd59;
    }

    .table tbody tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .table tbody tr:nth-child(odd) {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
        color: #f8f9fa !important;
        padding: 10px;
        vertical-align: middle;
        text-align: center;
    }

    .table a,
    .table span,
    .table strong,
    .table .text-muted,
    .table .text-secondary {
        color: #f8f9fa !important;
    }

    table .btn-primary {
        color: #ffdd59 !important;
        border: 1px solid #ffdd59;
        background-color: transparent;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    table .btn-primary:hover,
    .btn-primary:hover {
        background-color: #ffdd59;
        color: #222 !important;
    }

    .btn-danger {
        background-color: transparent;
        border: 1px solid #ff4d4d;
        color: #ff4d4d;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #ff4d4d;
        color: #fff;
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
    .table-responsive {
    width: 100%;
    overflow-x: auto;
}

.table {
    min-width: 600px; /* wymusza przewijanie na wąskich ekranach */
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
    word-break: break-word;
}


    @media (max-width: 768px) {
        .container {
            margin: 20px 10px;
            padding: 20px 15px;
        }

        h2 {
            font-size: 1.4rem;
        }

        .table {
            min-width: unset;
            font-size: 0.85rem;
        }

        .table th, .table td {
            padding: 8px;
        }

        .btn {
            font-size: 0.85rem;
            padding: 6px 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.2rem;
        }

        .btn {
            font-size: 0.8rem;
            padding: 5px 8px;
        }
    }

</style>


<canvas id="background-canvas"></canvas>

<div class="container">
    <h2 class="mb-4">Zarządzanie użytkownikami</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.users.destroyAll') }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć wszystkich użytkowników?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger mb-4">Usuń wszystkich użytkowników</button>
</form>


    <table class="table table-responsive table-striped align-middle text-center">
        <thead>
            <tr>
                <th>Imię i nazwisko</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edytuj</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Czy na pewno chcesz usunac tego uzytkownika?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Usuń</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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

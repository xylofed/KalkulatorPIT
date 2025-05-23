@extends('layouts.app')

@section('content')
<style>
    body, html {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: #f0f4ff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px; /* SZERSZE okno */
        margin: 60px auto 40px;
        background: rgba(38, 55, 112, 0.95);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 0 40px rgba(37, 117, 252, 0.85);
        color: #f0f4ff;
        min-height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    h1 {
        color: #ffdd59;
        text-shadow: 0 0 15px rgba(255, 221, 89, 0.9);
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 2.5rem;
    }

    /* Jasny i kontrastowy opis */
    p.text-muted {
    font-size: 1.25rem;
    color: #fff94d;
    font-weight: 600;
    margin-bottom: 2.5rem;
    text-shadow: 0 0 10px rgba(255, 221, 89, 0.9);
}




    label {
        font-weight: 600;
        color: #ffdd59;
        margin-top: 1.2rem;
        display: block;
        font-size: 1.1rem;
    }

    input.form-control,
    select.form-control {
        background: rgba(255, 255, 255, 0.15);
        border: 1.5px solid rgba(255, 221, 89, 0.85);
        color: #f0f4ff;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1.1rem;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        box-shadow: inset 0 1px 5px rgba(0,0,0,0.3);
        width: 100%;
        box-sizing: border-box;
    }

    input.form-control::placeholder {
        color: #e0e7ff99;
    }

    input.form-control:focus,
    select.form-control:focus {
        outline: none;
        border-color: #ffdd59;
        box-shadow: 0 0 15px #ffdd59;
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .form-check-label {
        color: #ffdd59;
        font-weight: 600;
        font-size: 1.1rem;
        user-select: none;
    }

    .form-check-input {
        width: 22px;
        height: 22px;
        border: 2px solid #ffdd59;
        background-color: transparent;
        cursor: pointer;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        margin-top: 0.2rem;
        margin-right: 10px;
        vertical-align: middle;
    }

    .form-check-input:checked {
        background-color: #ffdd59;
        border-color: #ffdd59;
    }

    .btn-primary {
        background-color: #ffdd59;
        border: none;
        color: #222;
        font-weight: 700;
        padding: 16px 30px;
        font-size: 1.25rem;
        border-radius: 14px;
        box-shadow: 0 7px 20px rgba(255, 221, 89, 0.9);
        margin-top: 3rem;
        width: 100%;
        transition: background-color 0.3s ease, color 0.3s ease;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #f5c700;
        color: #111;
        box-shadow: 0 10px 30px rgba(245, 199, 0, 1);
    }

    .alert-danger {
        background-color: rgba(255, 0, 0, 0.18);
        border-color: rgba(255, 0, 0, 0.55);
        color: #ffcccc;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 1.7rem;
        font-weight: 600;
        font-size: 1rem;
    }

    .alert-info {
        background-color: rgba(255, 221, 89, 0.22);
        border-color: rgba(255, 221, 89, 0.6);
        color: #fff9c4;
        border-radius: 12px;
        padding: 35px 40px;
        margin-top: 3rem;
        box-shadow: 0 0 30px rgba(255, 221, 89, 0.85);
        font-size: 1.15rem;
    }

    .alert-info h3 {
        color: #ffdd59;
        margin-bottom: 1.2rem;
        text-shadow: 0 0 15px rgba(255, 221, 89, 0.85);
    }

    ul.mb-0 li {
        margin-bottom: 8px;
    }
    select.form-control {
    background: rgba(255, 255, 255, 0.15);
    border: 1.5px solid rgba(255, 221, 89, 0.85);
    color: #ffdd59; /* jasny żółty dla czytelności */
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 1.1rem;
    transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
    box-shadow: inset 0 1px 5px rgba(0,0,0,0.3);
    width: 100%;
    box-sizing: border-box;
}

/* kolor tekstu opcji po wybraniu */
select.form-control option {
    color: #222; /* ciemny tekst w opcjach */
}

/* kolor tekstu po focus */
select.form-control:focus {
    outline: none;
    border-color: #ffdd59;
    box-shadow: 0 0 15px #ffdd59;
    background: rgba(255, 255, 255, 0.25);
    color: #222; /* ciemny kolor po focus */
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

    /* Responsive tweaks */
    @media (max-width: 960px) {
        .container {
            max-width: 95%;
            margin: 40px auto 30px;
            padding: 30px 25px;
            min-height: auto;
        }

        h1 {
            font-size: 2rem;
        }

        p.text-muted {
            font-size: 1.1rem;
        }

        .btn-primary {
            font-size: 1.1rem;
            padding: 14px 25px;
        }


    }
</style>

<canvas id="background-canvas"></canvas>

    <div class="container">
        <h1>Kalkulator PIT</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('pit-calculator.calculate') }}">
            @csrf

            <div class="form-group">
                <label for="income">Dochód</label>
                <input type="number" class="form-control" id="income" name="income" value="{{ old('income') }}" required>
            </div>

            <div class="form-group">
                <label for="expenses">Koszty uzyskania przychodu</label>
                <input type="number" class="form-control" id="expenses" name="expenses" value="{{ old('expenses') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="deductions">Ulgi i odliczenia</label>
                <input type="number" class="form-control" id="deductions" name="deductions" value="{{ old('deductions') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="tax_type">Typ opodatkowania</label>
                <select class="form-control" id="tax_type" name="tax_type" required>
                    <option value="scale" {{ old('tax_type') == 'scale' ? 'selected' : '' }}>Skala podatkowa (17%/32%)
                    </option>
                    <option value="flat" {{ old('tax_type') == 'flat' ? 'selected' : '' }}>Podatek liniowy (19%)</option>
                    <option value="ryczałt" {{ old('tax_type') == 'ryczałt' ? 'selected' : '' }}>Ryczałt (8.5%)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="children">Liczba dzieci</label>
                <input type="number" class="form-control" id="children" name="children" value="{{ old('children') }}">
            </div>

            <div class="form-group">
                <label for="social_insurance">Składki społeczne</label>
                <input type="number" class="form-control" id="social_insurance" name="social_insurance"
                    value="{{ old('social_insurance') }}">
            </div>

            <div class="form-group">
                <label for="health_insurance">Składki zdrowotne</label>
                <input type="number" class="form-control" id="health_insurance" name="health_insurance"
                    value="{{ old('health_insurance') }}">
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_married" name="is_married"
                    {{ old('is_married') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_married">Wspólne rozliczenie małżeńskie</label>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Oblicz PIT</button>
        </form>

        <!-- Wyświetlanie wyników obliczeń -->
        @if (session('taxableIncome') !== null && session('taxAmount') !== null)
            <div class="mt-4 alert alert-success">
                <h3>Wyniki kalkulacji</h3>
                <p><strong>Dochód do opodatkowania:</strong> {{ number_format(session('taxableIncome'), 2) }} PLN</p>
                <p><strong>Podatek do zapłaty:</strong> {{ number_format(session('taxAmount'), 2) }} PLN</p>
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

@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Panel administratora – historia zmian</h1>

        {{-- Komunikat sukcesu po usunięciu --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zamknij"></button>
            </div>
        @endif

        <div class="mb-3">
            <form action="{{ route('dashboard.clear-history') }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć całą historię zmian?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash3"></i> Wyczyść całą historię
                </button>
            </form>
        </div>

        @if ($histories->isEmpty())
            <div class="alert alert-info">Brak zapisanych zmian.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center table-custom-bg">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Użytkownik</th>
                            <th>Typ akcji</th>
                            <th>Kwota podatku</th>
                            <th style="min-width: 500px;">Szczegóły</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $labels = [
                                'income' => 'Przychód',
                                'expenses' => 'Koszty uzyskania',
                                'deductions' => 'Odliczenia',
                                'tax_type' => 'Typ podatku',
                                'children' => 'Liczba dzieci',
                                'social_insurance' => 'Składki ZUS',
                                'health_insurance' => 'Składki zdrowotne',
                                'is_married' => 'Wspólne rozliczenie',
                                'taxable_income' => 'Dochód do opodatkowania',
                                'tax_amount' => 'Kwota podatku',
                            ];

                            $actionLabels = [
                                'created' => 'Utworzono',
                                'updated' => 'Zaktualizowano',
                                'deleted' => 'Usunięto',
                            ];

                            function displayValue($key, $value)
                            {
                                if ($key === 'is_married') {
                                    return $value ? 'Tak' : 'Nie';
                                }
                                if ($key === 'tax_type') {
                                    $types = ['scale' => 'Skala', 'flat' => 'Liniowy', 'ryczałt' => 'Ryczałt'];
                                    return $types[$value] ?? $value;
                                }
                                if ($key === 'children') {
                                    return (int) $value;
                                }
                                if (is_numeric($value)) {
                                    return number_format($value, 2, ',', ' ') . ' zł';
                                }
                                return $value ?? '—';
                            }
                        @endphp

                        @foreach ($histories as $index => $history)
                            @php
                                // Parsowanie danych do tablic, jeśli to JSON lub tablica
                                $details = is_array($history->previous_values) ? $history->previous_values : (json_decode($history->previous_values, true) ?? []);
                                $newDetails = is_array($history->new_values) ? $history->new_values : (json_decode($history->new_values, true) ?? []);

                                $hasPrevious = !empty($details);
                                $hasCurrent = !empty($newDetails);

                                // Wyświetlamy kwotę podatku w zależności od akcji
                                $taxAmountToShow = 0;
                                if ($history->action === 'deleted') {
                                    $taxAmountToShow = $details['tax_amount'] ?? 0;
                                } elseif ($hasCurrent) {
                                    $taxAmountToShow = $newDetails['tax_amount'] ?? 0;
                                } elseif ($hasPrevious) {
                                    $taxAmountToShow = $details['tax_amount'] ?? 0;
                                }
                            @endphp

                            <tr>
                                <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $history->user->name ?? 'Brak' }}</td>
                                <td>{{ $actionLabels[$history->action] ?? ucfirst($history->action) }}</td>
                                <td>{{ number_format($taxAmountToShow, 2, ',', ' ') }} zł</td>
                                <td class="text-start">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-sm btn-outline-primary" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#detailsCollapse{{ $index }}"
                                            aria-expanded="false" aria-controls="detailsCollapse{{ $index }}">
                                            Pokaż szczegóły
                                        </button>
                                    </div>

                                    <div class="collapse mt-2" id="detailsCollapse{{ $index }}">
                                        @if ($history->action === 'created')
                                            @php
                                                // Dla created pokazujemy tylko nowe wartości
                                                $dataToShow = $newDetails;
                                            @endphp
                                            @if (!empty($dataToShow))
                                                <ul class="mb-0 list-unstyled">
                                                    @foreach ($labels as $key => $label)
                                                        @if(isset($dataToShow[$key]))
                                                        <li class="d-flex justify-content-between border-bottom py-1">
                                                            <strong>{{ $label }}:</strong>
                                                            <span>{{ displayValue($key, $dataToShow[$key]) }}</span>
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted">Brak danych do wyświetlenia.</div>
                                            @endif

                                        @elseif ($history->action === 'updated')
                                            @if ($hasPrevious || $hasCurrent)
                                                <ul class="mb-0 list-unstyled">
                                                    @foreach ($labels as $key => $label)
                                                        @php
                                                            $old = $details[$key] ?? null;
                                                            $new = $newDetails[$key] ?? null;
                                                        @endphp
                                                        @if($old !== null || $new !== null)
                                                        <li class="d-flex justify-content-between border-bottom py-1">
                                                            <strong>{{ $label }}:</strong>
                                                            <span>
                                                                @if ($old !== null && $new !== null && $old != $new)
                                                                    <span class="text-danger" title="Poprzednia wartość">
                                                                        {{ displayValue($key, $old) }}
                                                                    </span>
                                                                    <span class="text-success ms-2" title="Nowa wartość">
                                                                        <i class="bi bi-arrow-right"></i>
                                                                        {{ displayValue($key, $new) }}
                                                                    </span>
                                                                @else
                                                                    {{ displayValue($key, $new ?? $old) }}
                                                                @endif
                                                            </span>
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted">Brak danych do wyświetlenia.</div>
                                            @endif

                                        @elseif ($history->action === 'deleted')
                                            @if ($hasPrevious)
                                                <ul class="mb-0 list-unstyled">
                                                    @foreach ($labels as $key => $label)
                                                        @if(isset($details[$key]))
                                                        <li class="d-flex justify-content-between border-bottom py-1">
                                                            <strong>{{ $label }}:</strong>
                                                            <span>{{ displayValue($key, $details[$key]) }}</span>
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted">Brak danych do wyświetlenia.</div>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <form action="{{ route('tax-history.destroy', $history->id) }}" method="POST"
                                        onsubmit="return confirm('Czy na pewno chcesz usunąć tę historię?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                                    </form>
                                    @if ($history->user)
                                        <a href="{{ route('users.edit', $history->user->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i> Edytuj
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>




    <style>
        /* Twoje oryginalne style CSS */

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

        .table-bordered {
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        .table-bordered > :not(caption) > * > * {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* Ukrycie elementów DataTables (jeśli istnieją) */
        .dataTables_info,
        .text-muted {
            display: none !important;
        }

        .table-custom-bg {
    background: rgba(38, 55, 112, 0.4);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 8px 32px rgba(37, 117, 252, 0.4);
    border-radius: 16px;
    border: 1px solid rgba(255, 221, 89, 0.3);
}

.table-custom-bg,
.table-custom-bg th,
.table-custom-bg td {
    color: #e0e7ff !important;
}

.table-custom-bg thead {
    background-color: rgba(38, 55, 112, 0.7);
    color: #ffdd59 !important;
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

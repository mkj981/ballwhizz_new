<div wire:loading.class="opacity-50">

    <div class="card shadow-sm border-0">

        {{-- 🔥 HEADER --}}
        <div class="card-header bg-dark text-white py-3 d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">🏆 User Rankings</h3>

            <div class="d-flex align-items-center">

                {{-- Type --}}
                <select wire:model.live="type" class="form-select form-select-sm ranking-filter-select">
                    <option value="all">All Types</option>
                    <option value="cards">Cards</option>
                    <option value="prediction">Prediction</option>
                    <option value="trivia">Trivia</option>
                </select>

                {{-- Month Filter --}}
                <select wire:model.live="month" class="form-select form-select-sm ranking-filter-select">
                    <option value="">Select Month</option>
                    @for ($m = 8; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                    @for ($m = 1; $m <= 7; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>

                {{-- Week Filter --}}
                <select wire:model.live="week_id" class="form-select form-select-sm ranking-filter-select">
                    <option value="">Select Week</option>
                    @foreach ($weeks as $week)
                        <option value="{{ $week->id }}">
                            {{ $week->week_name }} ({{ $week->start_date }} → {{ $week->end_date }})
                        </option>
                    @endforeach
                </select>

            </div>
        </div>

        {{-- 📝 BODY --}}
        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th width="70">Rank</th>
                    <th>User ID</th>
                    <th>Name</th>

                    <th>Total Points</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $rank = ($records->currentPage() - 1) * $records->perPage() + 1;
                @endphp

                @foreach ($records as $row)
                    <tr>
                        {{-- Rank --}}
                        <td>{{ $rank++ }}</td>

                        {{-- User ID --}}
                        <td>{{ $row->user->id ?? '-' }}</td>

                        {{-- Name --}}
                        <td>{{ $row->user->name ?? 'Unknown' }}</td>

                        {{-- Username --}}


                        {{-- Total Points --}}
                        <td class="fw-bold">{{ number_format($row->total_points, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>
        </div>

    </div>

</div>

{{-- ------------------------------------------------------------
     CUSTOM SELECT STYLING
------------------------------------------------------------- --}}
@push('css')
    <style>
        .ranking-filter-select {
            margin-left: 8px;
            margin-right: 8px;
            min-width: 150px;
            background: #ffffff;
            color: #000000;
            border-radius: 4px;
            padding: 3px 6px;
        }

        .ranking-filter-select:hover {
            border-color: #0d6efd;
        }
    </style>
@endpush

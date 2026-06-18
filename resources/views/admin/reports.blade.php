<x-app-layout>
    <div class="bg-slate-50 min-h-screen pb-12">
        
        <!-- Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="flex flex-col gap-1.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold w-fit mb-2 border border-emerald-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Ringkasan Bisnis
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Performa {{ $periodLabel }}</h2>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Laporan Bisnis</p>
                    <p class="text-sm font-semibold text-slate-500 max-w-2xl">Pantau pendapatan, jumlah pemesanan, dan tingkat hunian kamar pada rentang waktu ini.</p>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.reports') }}" method="GET" class="relative">
                        <select name="filter" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl pl-4 pr-10 py-2 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer shadow-sm transition-colors">
                            <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week" {{ $filter === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="month" {{ $filter === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="3_months" {{ $filter === '3_months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                            <option value="year" {{ $filter === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </form>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-800 border border-slate-800 text-white rounded-xl hover:bg-slate-700 transition-all font-bold text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Quick Stats -->
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Main Stat Card -->
                <div class="col-span-2 lg:col-span-1 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 shadow-lg shadow-emerald-500/20 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-emerald-50 text-sm font-bold uppercase tracking-wider">Pendapatan Bersih</p>
                        <p class="mt-2 text-3xl font-black text-white truncate" title="Rp {{ number_format($revenueThisPeriod, 0, ',', '.') }}">Rp {{ number_format($revenueThisPeriod, 0, ',', '.') }}</p>
                        <div class="mt-4 inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/20 rounded-md backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-[11px] font-bold text-white">Lunas & DP</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200/60 p-5 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Total Pemesanan</p>
                        <p class="mt-2 text-3xl font-black text-slate-800">{{ $bookingCount }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">Pemesanan</span>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200/60 p-5 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Omzet Kotor</p>
                        <p class="mt-2 text-2xl font-black text-slate-800 truncate" title="Rp {{ number_format($grossSales, 0, ',', '.') }}">Rp {{ number_format($grossSales, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">Nilai Pesanan</span>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200/60 p-5 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Tunggu DP</p>
                        <p class="mt-2 text-3xl font-black text-amber-600">{{ $pendingPaymentCount }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Follow Up
                        </span>
                    </div>
                </div>
            </section>

            <!-- Middle Section (Charts) -->
            <section class="grid gap-6 lg:grid-cols-3">
                
                <!-- Revenue Chart -->
                <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Grafik Pendapatan</h3>
                            <p class="text-sm font-semibold text-slate-500">Tren pemasukan bersih (DP & Pelunasan) selama periode ini.</p>
                        </div>
                    </div>
                    <div class="p-2 flex-1 w-full min-h-[350px]">
                        <div id="revenue-chart"></div>
                    </div>
                </div>

                <!-- Source Stats -->
                <div class="rounded-2xl bg-white border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-800">Sumber Tamu</h3>
                        <p class="text-sm font-semibold text-slate-500">Asal informasi yang membawa tamu.</p>
                    </div>
                    <div class="p-6 flex-1 space-y-5">
                        @forelse ($sourceStats as $source)
                            <div class="group">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <p class="text-sm font-bold capitalize text-slate-700">{{ [
                                        'Instagram' => 'Instagram',
                                        'Google' => 'Google',
                                        'Friend' => 'Rekomendasi Teman',
                                        'TikTok' => 'TikTok',
                                        'Walk-in' => 'Datang Langsung',
                                        'Other' => 'Lainnya',
                                        'Internal admin' => 'Dicatat oleh Admin',
                                    ][$source->source] ?? str_replace('_', ' ', $source->source) }}</p>
                                    <span class="text-xs font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md">{{ $source->total }}</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-sky-400 to-sky-500 rounded-full group-hover:brightness-110 transition-all" style="width: {{ $sourceTotal > 0 ? min(100, ($source->total / $sourceTotal) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm font-bold text-slate-400 py-8">Belum ada data</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Bottom Section (Occupancy & Rooms) -->
            <section class="grid gap-6 lg:grid-cols-2">
                <!-- Room Occupancy -->
                <div class="rounded-2xl bg-white border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Tingkat Hunian Kamar</h3>
                            <p class="text-sm font-semibold text-slate-500">Persentase malam yang terisi.</p>
                        </div>
                    </div>
                    <div class="p-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($occupancyStats as $index => $room)
                            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 hover:bg-slate-50 transition-colors group flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <h4 class="text-sm font-black text-slate-800 leading-tight">
                                            {{ $room['name'] }}
                                        </h4>
                                        <p class="text-lg font-black leading-none shrink-0 {{ $room['occupancy_rate'] >= 70 ? 'text-emerald-600' : ($room['occupancy_rate'] >= 40 ? 'text-sky-600' : 'text-slate-500') }}">
                                            {{ $room['occupancy_rate'] }}%
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 mb-3">
                                        @if($index === 0 && $room['occupancy_rate'] > 0)
                                            <span class="bg-amber-100 text-amber-700 text-[10px] font-black uppercase px-2 py-0.5 rounded flex-shrink-0">Top</span>
                                        @endif
                                        <p class="text-xs font-bold text-slate-500">{{ $room['occupied_nights'] }} / {{ $room['available_nights'] }} malam</p>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5 mt-auto">
                                    <div class="h-1.5 rounded-full {{ $room['occupancy_rate'] >= 70 ? 'bg-emerald-500' : ($room['occupancy_rate'] >= 40 ? 'bg-sky-500' : 'bg-slate-300') }}" style="width: {{ min(100, $room['occupancy_rate']) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm font-bold text-slate-400 py-8 sm:col-span-2">Belum ada data kamar</p>
                        @endforelse
                    </div>
                </div>

                <!-- Room Sales & Status -->
                <div class="flex flex-col gap-6">
                    <div class="rounded-2xl bg-white border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] overflow-hidden flex-1">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-800">Performa Penjualan Kamar</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-6 py-3 border-b border-slate-100">Kamar</th>
                                        <th class="px-6 py-3 border-b border-slate-100">Pemesanan</th>
                                        <th class="px-6 py-3 border-b border-slate-100 text-right">Omzet</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($roomStats as $room)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ $room->name }}</td>
                                            <td class="px-6 py-3.5 font-semibold text-slate-600">{{ $room->booking_count }}</td>
                                            <td class="px-6 py-3.5 text-right font-black text-slate-800">Rp {{ number_format($room->gross_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- ApexCharts Script -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Pendapatan',
                    data: {!! json_encode($chartData) !!}
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#10b981'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: {!! json_encode($chartLabels) !!},
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                        },
                        style: {
                            colors: '#64748b',
                            fontSize: '11px',
                            fontWeight: 600,
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();
        });
    </script>
</x-app-layout>


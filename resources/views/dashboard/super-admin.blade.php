<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Super Admin - Dafano Villa</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for quick preview) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans" x-data="{ sidebarOpen: true }">
    <!-- Layout Wrapper -->
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white border-r border-slate-100 flex flex-col transition-all duration-300">
            <!-- Logo area -->
            <div class="h-16 flex items-center px-6 border-b border-slate-100 justify-between">
                <span x-show="sidebarOpen" class="text-xl font-bold text-slate-800 tracking-tight">Dafano <span class="text-emerald-700">Villa</span></span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-emerald-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen">Ringkasan</span>
                </a>
                
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen">Reservasi</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="sidebarOpen">Manajemen Villa</span>
                </a>
                
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="sidebarOpen">Pelanggan</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="sidebarOpen">Laporan</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarOpen">Keluar</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
            <!-- Header -->
            <header class="h-16 bg-slate-50/80 backdrop-blur-sm border-b border-slate-100 flex items-center justify-between px-8 z-10">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-semibold text-slate-800">Super Admin <span class="text-slate-500 font-normal text-lg">(Ibu Tiwi)</span></h1>
                </div>
                
                <div class="flex items-center gap-6">
                    <!-- Date -->
                    <div class="hidden md:flex items-center text-sm font-medium text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Selasa, 26 Mei 2026</span>
                    </div>

                    <!-- Notification -->
                    <button class="relative p-2 text-slate-400 hover:text-emerald-700 transition-colors rounded-full hover:bg-emerald-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    </button>
                    
                    <!-- Profile -->
                    <div class="h-9 w-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold border border-emerald-200 shadow-sm">
                        IT
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto space-y-8">
                    
                    <!-- Top Widgets -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Widget 1: Pendapatan -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5">
                            <div class="h-14 w-14 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Pendapatan Bulan Ini</p>
                                <h3 class="text-2xl font-bold text-slate-800">Rp 45.500.000</h3>
                            </div>
                        </div>

                        <!-- Widget 2: Menunggu Validasi -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5">
                            <div class="h-14 w-14 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center relative">
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Menunggu Validasi</p>
                                <h3 class="text-2xl font-bold text-slate-800">5 <span class="text-sm font-medium text-slate-500">Reservasi</span></h3>
                            </div>
                        </div>

                        <!-- Widget 3: Okupansi -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5">
                            <div class="h-14 w-14 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Tingkat Okupansi</p>
                                <h3 class="text-2xl font-bold text-slate-800">78%</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">Reservasi Terbaru</h2>
                            <button class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">Lihat Semua</button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50">
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100">Kode Pemesanan</th>
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100">Tamu</th>
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100">Tanggal Masuk</th>
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100">Total Harga</th>
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100">Status</th>
                                        <th class="py-4 px-6 text-sm font-medium text-slate-500 border-b border-slate-100 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Row 1 (Pending) -->
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">#RES-0992</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="font-medium text-slate-800">Bpk. Ahmad Wijaya</div>
                                            <div class="text-xs text-slate-500 mt-0.5">0812-3456-7890</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="text-slate-700">28 Mei 2026</div>
                                            <div class="text-xs text-slate-500 mt-0.5">2 Malam</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">Rp 3.500.000</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 ring-2 ring-amber-200/50 shadow-sm shadow-amber-200/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                Menunggu Validasi
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100 text-right">
                                            <button class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 hover:-translate-y-0.5 shadow-sm hover:shadow-md transition-all duration-200">
                                                Validasi Pembayaran
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Row 2 (Lunas) -->
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">#RES-0991</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="font-medium text-slate-800">Ibu Sarah Monica</div>
                                            <div class="text-xs text-slate-500 mt-0.5">0819-8765-4321</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="text-slate-700">30 Mei 2026</div>
                                            <div class="text-xs text-slate-500 mt-0.5">1 Malam</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">Rp 1.750.000</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                                                Lunas
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100 text-right">
                                            <button class="inline-flex items-center justify-center px-4 py-2 bg-white text-slate-700 border border-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 hover:text-slate-900 hover:-translate-y-0.5 shadow-sm transition-all duration-200">
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Row 3 (Lunas) -->
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">#RES-0990</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="font-medium text-slate-800">Kel. Budi Santoso</div>
                                            <div class="text-xs text-slate-500 mt-0.5">0852-1122-3344</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <div class="text-slate-700">01 Jun 2026</div>
                                            <div class="text-xs text-slate-500 mt-0.5">3 Malam</div>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="font-medium text-slate-800">Rp 5.250.000</span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                                                Lunas
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-100 text-right">
                                            <button class="inline-flex items-center justify-center px-4 py-2 bg-white text-slate-700 border border-slate-200 text-sm font-medium rounded-lg hover:bg-slate-50 hover:text-slate-900 hover:-translate-y-0.5 shadow-sm transition-all duration-200">
                                                Lihat Detail
                                            </button>
                                            <button class="inline-flex items-center justify-center px-3 py-2 ml-2 bg-white text-emerald-600 border border-emerald-200 text-sm font-medium rounded-lg hover:bg-emerald-50 hover:-translate-y-0.5 shadow-sm transition-all duration-200">
                                                Input Diskon
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

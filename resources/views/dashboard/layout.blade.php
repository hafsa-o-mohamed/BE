<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة العقارات</title>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="font-arabic">
    <!-- Main Header -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="flex items-center justify-between h-16 px-4 pr-4">
            <div class="flex items-center">
                <img src="{{ asset('logo.png') }}" alt="Tmahur Group Logo" class="h-10 w-auto">
                <span class="ml-3 text-xl font-semibold text-gray-800">Tmahur</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-gray-800 mx-3 relative">
                        <i class='bx bxs-bell text-2xl'></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">0</span>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                        <div class="px-4 py-2 border-b">
                            <h3 class="text-lg font-semibold">الإشعارات</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-2 hover:bg-gray-100">
                                <p class="text-sm text-gray-600">لا توجد إشعارات جديدة</p>
                            </div>
                        </div>
                        <div class="px-4 py-2 border-t">
                            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">عرض كل الإشعارات</a>
                        </div>
                    </div>
                </div>
                <i class='bx bxs-user-circle text-2xl text-gray-800'></i>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-gray-800">{{ Auth::user()->name }}</button>
                </form>
            </div>
        </div>
    </header>

    <script>
        document.getElementById('userDropdown').addEventListener('click', function() {
            var dropdownMenu = document.getElementById('dropdownMenu');
            dropdownMenu.classList.toggle('hidden');
        });
    </script>

    <!-- Adjust main container to account for fixed header -->
    <div class="flex pt-16">
        <!-- Sidebar -->
        <aside class="fixed right-0 top-0 pt-16 w-48 h-full bg-gray-800">
            <nav class="flex-1 px-2 space-y-1 pt-5">
                <a href="{{ route('dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-dashboard mr-3 text-xl'></i> لوحة التحكم
                </a>
                <a href="{{ route('projects.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-building-house mr-3 text-xl'></i> المشاريع
                </a>
                <div class="relative">
                    <button id="buildingsToggle" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md w-full text-left">
                        <i class='bx bxs-buildings mr-3 text-xl'></i> المباني
                        <i class='bx bx-chevron-down ml-auto'></i>
                    </button>
                    <div id="buildingsSubMenu" class="hidden pl-8 space-y-1">
                        <a href="{{ route('buildings.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-buildings mr-3 text-xl'></i> قائمة المباني
                        </a>
                        <a href="{{ route('water.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-water mr-3 text-xl'></i> فواتير المياه
                        </a>
                        <a href="{{ route('electricity.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-bolt mr-3 text-xl'></i> فواتير الكهرباء
                        </a>
                    </div>
                </div>
                <a href="{{ route('apartments.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-home mr-3 text-xl'></i> الشقق
                </a>
                <a href="{{ route('contracts.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-file-doc mr-3 text-xl'></i> العقود
                </a>
                <div class="relative">
                    <button id="servicesToggle" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md w-full text-left">
                        <i class='bx bxs-wrench mr-3 text-xl'></i> الخدمات
                        <i class='bx bx-chevron-down ml-auto'></i>
                    </button>
                    <div id="servicesSubMenu" class="hidden pl-8 space-y-1">
                        <a href="{{ route('services.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-wrench mr-3 text-xl'></i> قائمة الخدمات
                        </a>
                        <a href="{{ route('dashboard.service-requests.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-file-doc mr-3 text-xl'></i> طلبات الخدمات
                        </a>
                        <a href="{{ route('services.duepayments') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-6 py-2 text-sm font-medium rounded-md">
                            <i class='bx bxs-file-doc mr-3 text-xl'></i> الفواتير المستحقة
                        </a>
                    </div>
                </div>
                <a href="{{ route('owners.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-crown mr-3 text-xl'></i> الملاك
                </a>
                <a href="{{ route('users.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-user mr-3 text-xl'></i> المستخدمين
                </a>
                <a href="{{ route('bills.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-file-doc mr-3 text-xl'></i> الفواتير
                </a>
                <a href="{{ route('suggestions.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-message-dots mr-3 text-xl'></i> الاقتراحات والشكاوي
                </a>
                <a href="{{ route('notifications.index') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <i class='bx bxs-bell mr-3 text-xl'></i> الإشعارات
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class='bx bxs-log-out-circle mr-3 text-xl'></i> تسجيل الخروج
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 min-h-screen bg-gray-100" style="margin-right: 13rem;">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
    @livewireScripts
    @livewireStyles
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.getElementById('servicesToggle').addEventListener('click', function() {
            var subMenu = document.getElementById('servicesSubMenu');
            subMenu.classList.toggle('hidden');
        });
    </script>

    <script>
        document.getElementById('buildingsToggle').addEventListener('click', function() {
            var subMenu = document.getElementById('buildingsSubMenu');
            subMenu.classList.toggle('hidden');
        });
    </script>

@yield('scripts')
</body>
</html>
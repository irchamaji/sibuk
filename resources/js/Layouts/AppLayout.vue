<template>
    <!-- Layout utama aplikasi dengan sidebar navigasi -->
    <div class="min-h-screen bg-gray-50 flex">

        <!-- Sidebar navigasi -->
        <aside class="w-64 min-h-screen flex flex-col shadow-sm" style="background-color: #895129;">
            <!-- Logo di sidebar -->
            <div class="px-6 py-5 border-b border-white/20">
                <h1 class="text-white text-xl font-bold">SIBUK</h1>
                <p class="text-white/60 text-xs mt-0.5">Sistem Izin Usaha Kecil</p>
            </div>

            <!-- Info pengguna yang login -->
            <div class="px-4 py-4 border-b border-white/20">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
                         style="background-color: #FFE1CC; color: #895129;">
                        {{ userInitial }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ user.name }}</p>
                        <p class="text-white/60 text-xs capitalize">{{ roleLabel }}</p>
                    </div>
                </div>
            </div>

            <!-- Menu navigasi -->
            <nav class="flex-1 px-3 py-4 space-y-1">
                <Link :href="route('beranda')"
                      class="nav-item"
                      :class="{ 'nav-item-active': isActive('beranda') }">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </Link>

                <!-- Menu untuk pengguna biasa -->
                <template v-if="user.role === 'pengguna'">
                    <Link :href="route('perizinan.create')"
                          class="nav-item"
                          :class="{ 'nav-item-active': isActive('perizinan.create') }">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Izin Baru
                    </Link>
                    <Link :href="route('perizinan.index')"
                          class="nav-item"
                          :class="{ 'nav-item-active': isActive('perizinan.index') }">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Perizinan
                    </Link>
                </template>

                <!-- Menu untuk admin-pemda dan superadmin -->
                <template v-if="user.role === 'admin-pemda' || user.role === 'superadmin'">
                    <Link :href="route('admin.perizinan.index')"
                          class="nav-item"
                          :class="{ 'nav-item-active': isActive('admin.perizinan.index') }">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Kelola Perizinan
                    </Link>
                </template>

                <!-- Menu superadmin -->
                <template v-if="user.role === 'superadmin'">
                    <Link :href="route('admin.akun.index')"
                          class="nav-item"
                          :class="{ 'nav-item-active': isActive('admin.akun.index') }">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manajemen Akun
                    </Link>
                </template>

                <Link :href="route('akun.pengaturan')"
                      class="nav-item"
                      :class="{ 'nav-item-active': isActive('akun.pengaturan') }">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Pengaturan Akun
                </Link>
            </nav>

            <!-- Tombol logout di bawah sidebar -->
            <div class="px-3 py-4 border-t border-white/20">
                <form @submit.prevent="logout">
                    <button type="submit"
                            class="w-full flex items-center px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/10 hover:text-white transition-colors text-sm font-medium">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten utama -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header atas -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">{{ title || 'SIBUK' }}</h2>
                <span class="text-sm text-gray-500">{{ currentDate }}</span>
            </header>

            <!-- Flash messages di konten -->
            <div v-if="$page.props.flash?.success" class="mx-6 mt-4">
                <div class="bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ $page.props.flash.success }}
                </div>
            </div>
            <div v-if="$page.props.flash?.error" class="mx-6 mt-4">
                <div class="bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg text-sm">
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Konten halaman -->
            <main class="flex-1 p-6">
                <slot />
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200 px-6 py-3 text-center text-xs text-gray-400">
                Kode sumber aplikasi ini bisa dilihat
                <a href="https://github.com/irchamaji/sibuk"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="font-medium hover:underline"
                   style="color: #895129;">disini</a>.
            </footer>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Inisial nama pengguna untuk avatar
const userInitial = computed(() => {
    return user.value?.name?.charAt(0).toUpperCase() || 'U';
});

// Label role dalam bahasa Indonesia
const roleLabel = computed(() => {
    const roles = {
        'pengguna':    'Pengguna',
        'admin-pemda': 'Admin Pemda',
        'superadmin':  'Super Admin',
    };
    return roles[user.value?.role] || user.value?.role;
});

// Tanggal sekarang untuk header
const currentDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});

// Cek apakah route sedang aktif
const isActive = (routeName) => {
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
};

// Manajemen Sesi: proses logout
const logout = () => {
    router.post(route('logout'));
};
</script>

<style scoped>
.nav-item {
    @apply flex items-center px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/10 hover:text-white transition-colors text-sm font-medium;
}
.nav-item-active {
    background-color: rgba(255, 255, 255, 0.2);
    @apply text-white;
}
</style>

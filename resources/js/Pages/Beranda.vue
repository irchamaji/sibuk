<template>
    <AppLayout title="Beranda">
        <div class="space-y-6">
            <!-- Sambutan pengguna -->
            <div class="rounded-xl p-6 text-white" style="background: linear-gradient(135deg, #895129 0%, #6b3e1f 100%);">
                <h3 class="text-xl font-bold">Selamat Datang, {{ user.name }}!</h3>
                <p class="text-white/80 text-sm mt-1">
                    {{ roleLabel }} — Sistem Informasi Buka Usaha Kecil
                </p>
                <p class="text-white/60 text-xs mt-1">Pemerintah Daerah Kota Contoh</p>
            </div>

            <!-- Statistik perizinan -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="card text-center">
                    <p class="text-3xl font-bold" style="color: #895129;">{{ statistik.total }}</p>
                    <p class="text-gray-500 text-sm mt-1">Total Pengajuan</p>
                </div>
                <div class="card text-center">
                    <p class="text-3xl font-bold" style="color: #29638A;">{{ statistik.pengajuan }}</p>
                    <p class="text-gray-500 text-sm mt-1">Menunggu</p>
                </div>
                <div class="card text-center">
                    <p class="text-3xl font-bold" style="color: #298A72;">{{ statistik.diizinkan }}</p>
                    <p class="text-gray-500 text-sm mt-1">Diizinkan</p>
                </div>
                <div class="card text-center">
                    <p class="text-3xl font-bold text-red-600">{{ statistik.ditolak }}</p>
                    <p class="text-gray-500 text-sm mt-1">Ditolak</p>
                </div>
            </div>

            <!-- Aksi cepat untuk pengguna biasa -->
            <template v-if="user.role === 'pengguna'">
                <h4 class="text-base font-semibold text-gray-700">Aksi Cepat</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Link :href="route('perizinan.create')"
                          class="card hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #FFE1CC;">
                                <svg class="w-6 h-6" style="color: #895129;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Ajukan Izin Baru</p>
                                <p class="text-gray-500 text-xs">Daftarkan usaha UMKM Anda</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('perizinan.index')"
                          class="card hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #FFE1CC;">
                                <svg class="w-6 h-6" style="color: #895129;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Lihat Perizinan</p>
                                <p class="text-gray-500 text-xs">Status pengajuan izin Anda</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('akun.pengaturan')"
                          class="card hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #FFE1CC;">
                                <svg class="w-6 h-6" style="color: #895129;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Pengaturan Akun</p>
                                <p class="text-gray-500 text-xs">Ubah profil dan password</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </template>

            <!-- Aksi cepat untuk admin -->
            <template v-if="user.role === 'admin-pemda' || user.role === 'superadmin'">
                <h4 class="text-base font-semibold text-gray-700">Aksi Cepat</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Link :href="route('admin.perizinan.index')"
                          class="card hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #FFE1CC;">
                                <svg class="w-6 h-6" style="color: #895129;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Kelola Perizinan</p>
                                <p class="text-gray-500 text-xs">Tinjau dan setujui pengajuan izin</p>
                            </div>
                        </div>
                    </Link>

                    <Link v-if="user.role === 'superadmin'" :href="route('admin.akun.index')"
                          class="card hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color: #FFE1CC;">
                                <svg class="w-6 h-6" style="color: #895129;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Manajemen Akun</p>
                                <p class="text-gray-500 text-xs">Kelola akun pengguna sistem</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    statistik: Object,
});

const user = computed(() => usePage().props.auth.user);

const roleLabel = computed(() => {
    const labels = {
        'pengguna':    'Pengguna',
        'admin-pemda': 'Admin Pemerintah Daerah',
        'superadmin':  'Super Administrator',
    };
    return labels[user.value?.role] || '';
});
</script>

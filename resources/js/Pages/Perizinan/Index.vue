<template>
    <AppLayout title="Perizinan Saya">
        <div class="space-y-4">
            <!-- Header dengan tombol ajukan -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Perizinan Saya</h3>
                    <p class="text-gray-500 text-sm">Semua pengajuan izin usaha yang telah Anda ajukan</p>
                </div>
                <Link :href="route('perizinan.create')" class="btn-primary text-sm">
                    + Ajukan Baru
                </Link>
            </div>

            <!-- Tabel daftar perizinan -->
            <div class="card p-0 overflow-hidden">
                <div v-if="perizinans.length === 0" class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="font-medium">Belum ada perizinan</p>
                    <p class="text-sm mt-1">
                        <Link :href="route('perizinan.create')" class="hover:underline" style="color: #895129;">
                            Ajukan izin usaha pertama Anda
                        </Link>
                    </p>
                </div>

                <table v-else class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100" style="background-color: #FFE1CC;">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">No</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Usaha</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Lokasi</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Omzet</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(item, index) in perizinans" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <!-- ID sequential yang ditampilkan (IDOR vector) -->
                            <td class="px-4 py-3 text-sm text-gray-500">{{ item.id }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-800">{{ item.nama_usaha }}</p>
                                <p class="text-xs text-gray-500">{{ item.nama_pemohon }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ item.lokasi_usaha }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ formatRupiah(item.omzet) }}</td>
                            <td class="px-4 py-3">
                                <span :class="statusBadgeClass(item.status)">{{ item.status }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(item.created_at) }}</td>
                            <td class="px-4 py-3">
                                <Link :href="route('perizinan.show', item.id)"
                                      class="text-sm font-medium hover:underline"
                                      style="color: #29638A;">
                                    Detail
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    perizinans: Array,
});

// Format angka ke rupiah
const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(angka);
};

// Format tanggal ke bahasa Indonesia
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

// Kelas CSS badge status
const statusBadgeClass = (status) => {
    const map = {
        'Pengajuan': 'badge-pengajuan',
        'Diizinkan': 'badge-diizinkan',
        'Ditolak':   'badge-ditolak',
    };
    return map[status] || 'badge-pengajuan';
};
</script>

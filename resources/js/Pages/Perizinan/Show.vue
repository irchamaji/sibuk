<template>
    <AppLayout title="Detail Perizinan">
        <div class="max-w-2xl space-y-4">
            <!-- Tombol kembali -->
            <Link :href="route('perizinan.index')"
                  class="inline-flex items-center text-sm hover:underline"
                  style="color: #29638A;">
                ← Kembali ke Daftar Perizinan
            </Link>

            <div class="card">
                <!-- Header status -->
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ perizinan.nama_usaha }}</h3>
                        <p class="text-gray-500 text-sm">ID Pengajuan: #{{ perizinan.id }}</p>
                    </div>
                    <span :class="statusBadgeClass(perizinan.status)" class="text-sm px-3 py-1">
                        {{ perizinan.status }}
                    </span>
                </div>

                <!-- Detail data perizinan -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Pemohon</p>
                            <p class="text-gray-800 mt-0.5">{{ perizinan.nama_pemohon }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Usaha</p>
                            <p class="text-gray-800 mt-0.5">{{ perizinan.nama_usaha }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lokasi Usaha</p>
                        <p class="text-gray-800 mt-0.5">{{ perizinan.lokasi_usaha }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Omzet per Bulan</p>
                            <p class="text-gray-800 mt-0.5 font-medium">{{ formatRupiah(perizinan.omzet) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal Pengajuan</p>
                            <p class="text-gray-800 mt-0.5">{{ formatDate(perizinan.created_at) }}</p>
                        </div>
                    </div>

                    <!-- File surat kepemilikan -->
                    <div v-if="perizinan.surat_kepemilikan">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Surat Kepemilikan Usaha</p>
                        <a :href="'/storage/' + perizinan.surat_kepemilikan"
                           target="_blank"
                           class="inline-flex items-center text-sm mt-0.5 hover:underline"
                           style="color: #29638A;">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Unduh Dokumen
                        </a>
                    </div>

                    <!-- Catatan admin (jika ada) -->
                    <div v-if="perizinan.catatan_admin"
                         class="rounded-lg p-3"
                         :class="perizinan.status === 'Ditolak' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'">
                        <p class="text-xs font-medium uppercase tracking-wide mb-1"
                           :class="perizinan.status === 'Ditolak' ? 'text-red-600' : 'text-green-600'">
                            Catatan Admin
                        </p>
                        <p class="text-sm text-gray-700">{{ perizinan.catatan_admin }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    perizinan: Object,
});

const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(angka);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const statusBadgeClass = (status) => {
    const map = {
        'Pengajuan': 'badge-pengajuan',
        'Diizinkan': 'badge-diizinkan',
        'Ditolak':   'badge-ditolak',
    };
    return map[status] || 'badge-pengajuan';
};
</script>

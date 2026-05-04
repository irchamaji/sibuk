<template>
    <AppLayout title="Detail Perizinan (Admin)">
        <div class="max-w-2xl space-y-4">
            <Link :href="route('admin.perizinan.index')"
                  class="inline-flex items-center text-sm hover:underline"
                  style="color: #29638A;">
                ← Kembali ke Daftar Perizinan
            </Link>

            <!-- Detail data perizinan -->
            <div class="card">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ perizinan.nama_usaha }}</h3>
                        <p class="text-gray-500 text-sm">ID: #{{ perizinan.id }}</p>
                    </div>
                    <span :class="statusBadgeClass(perizinan.status)" class="text-sm px-3 py-1">
                        {{ perizinan.status }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="rounded-lg p-3" style="background-color: #FFE1CC;">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Data Pemohon</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-500">Nama</p>
                                <p class="text-sm font-medium text-gray-800">{{ perizinan.user?.name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm text-gray-800">{{ perizinan.user?.email }}</p>
                            </div>
                        </div>
                    </div>

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

                    <!-- Link dokumen (File Upload) -->
                    <div v-if="perizinan.surat_kepemilikan">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dokumen Surat Kepemilikan</p>
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
                </div>
            </div>

            <!-- Form ubah status (admin action) -->
            <div class="card">
                <h4 class="text-base font-semibold text-gray-800 mb-4">Tindakan Admin</h4>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Pilih status perizinan -->
                    <div>
                        <label class="form-label">Ubah Status Perizinan</label>
                        <select v-model="form.status"
                                class="input-field"
                                :class="{ 'border-red-500': form.errors.status }">
                            <option value="Pengajuan">Pengajuan (Belum Ditinjau)</option>
                            <option value="Diizinkan">Diizinkan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                        <p v-if="form.errors.status" class="text-red-600 text-xs mt-1">{{ form.errors.status }}</p>
                    </div>

                    <!-- Catatan admin -->
                    <div>
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea
                            v-model="form.catatan_admin"
                            rows="3"
                            placeholder="Tambahkan catatan untuk pemohon..."
                            class="input-field resize-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit"
                                class="btn-primary"
                                :disabled="form.processing">
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Keputusan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    perizinan: Object,
});

// Form untuk admin mengubah status perizinan
const form = useForm({
    status:        props.perizinan.status,
    catatan_admin: props.perizinan.catatan_admin || '',
});

const submit = () => {
    form.put(route('admin.perizinan.update', props.perizinan.id));
};

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

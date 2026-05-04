<template>
    <AppLayout title="Kelola Perizinan">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Kelola Semua Perizinan</h3>
                    <p class="text-gray-500 text-sm">Tinjau dan ubah status pengajuan izin usaha</p>
                </div>
                <!-- Filter status -->
                <div class="flex items-center space-x-2">
                    <select v-model="selectedStatus"
                            @change="filterByStatus"
                            class="input-field text-sm w-auto">
                        <option value="">Semua Status</option>
                        <option value="Pengajuan">Pengajuan</option>
                        <option value="Diizinkan">Diizinkan</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>

            <div class="card p-0 overflow-hidden">
                <div v-if="perizinans.length === 0" class="p-8 text-center text-gray-500">
                    <p>Tidak ada data perizinan.</p>
                </div>

                <table v-else class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100" style="background-color: #FFE1CC;">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">ID</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Pemohon</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Usaha</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Omzet</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in perizinans" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">#{{ item.id }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-800">{{ item.nama_pemohon }}</p>
                                <p class="text-xs text-gray-500">{{ item.user?.email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800">{{ item.nama_usaha }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ item.lokasi_usaha }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ formatRupiah(item.omzet) }}</td>
                            <td class="px-4 py-3">
                                <span :class="statusBadgeClass(item.status)">{{ item.status }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(item.created_at) }}</td>
                            <td class="px-4 py-3">
                                <Link :href="route('admin.perizinan.show', item.id)"
                                      class="text-sm font-medium hover:underline"
                                      style="color: #29638A;">
                                    Tinjau
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
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    perizinans:   Array,
    filterStatus: String,
});

const selectedStatus = ref(props.filterStatus || '');

// Filter tabel berdasarkan status
const filterByStatus = () => {
    router.get(route('admin.perizinan.index'), { status: selectedStatus.value }, { preserveState: true });
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
        month: 'short',
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

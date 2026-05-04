<template>
    <AppLayout title="Ajukan Izin Usaha">
        <div class="max-w-2xl">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Form Pengajuan Izin Usaha UMKM</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Isi semua data di bawah untuk mengajukan izin usaha kecil menengah Anda.
                </p>

                <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">
                    <!-- Validasi Input Form: Nama Pemohon -->
                    <div>
                        <label class="form-label">Nama Pemohon <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.nama_pemohon"
                            type="text"
                            placeholder="Nama lengkap sesuai KTP"
                            class="input-field"
                            :class="{ 'border-red-500': form.errors.nama_pemohon }"
                        />
                        <p v-if="form.errors.nama_pemohon" class="text-red-600 text-xs mt-1">{{ form.errors.nama_pemohon }}</p>
                    </div>

                    <!-- Validasi Input Form: Nama Usaha -->
                    <div>
                        <label class="form-label">Nama Usaha <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.nama_usaha"
                            type="text"
                            placeholder="Nama usaha yang akan didaftarkan"
                            class="input-field"
                            :class="{ 'border-red-500': form.errors.nama_usaha }"
                        />
                        <p v-if="form.errors.nama_usaha" class="text-red-600 text-xs mt-1">{{ form.errors.nama_usaha }}</p>
                    </div>

                    <!-- Validasi Input Form: Lokasi Usaha -->
                    <div>
                        <label class="form-label">Lokasi Usaha <span class="text-red-500">*</span></label>
                        <textarea
                            v-model="form.lokasi_usaha"
                            rows="3"
                            placeholder="Alamat lengkap lokasi usaha (jalan, kelurahan, kecamatan, kota)"
                            class="input-field resize-none"
                            :class="{ 'border-red-500': form.errors.lokasi_usaha }"
                        ></textarea>
                        <p v-if="form.errors.lokasi_usaha" class="text-red-600 text-xs mt-1">{{ form.errors.lokasi_usaha }}</p>
                    </div>

                    <!-- Validasi Input Form: Omzet (hanya angka) -->
                    <div>
                        <label class="form-label">Omzet per Bulan (Rp) <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.omzet"
                            type="number"
                            min="0"
                            placeholder="Contoh: 5000000"
                            class="input-field"
                            :class="{ 'border-red-500': form.errors.omzet }"
                        />
                        <p class="text-gray-500 text-xs mt-1">Masukkan angka saja tanpa titik atau koma.</p>
                        <p v-if="form.errors.omzet" class="text-red-600 text-xs mt-1">{{ form.errors.omzet }}</p>
                    </div>

                    <!-- File Upload: Surat Kepemilikan Usaha (tanpa validasi format) -->
                    <div>
                        <label class="form-label">Surat Kepemilikan Usaha</label>
                        <div class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-gray-400 transition-colors">
                            <input
                                ref="fileInput"
                                type="file"
                                class="hidden"
                                @change="handleFileChange"
                            />
                            <div class="text-center cursor-pointer" @click="fileInput.click()">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p v-if="selectedFileName" class="text-sm font-medium" style="color: #895129;">{{ selectedFileName }}</p>
                                <p v-else class="text-sm text-gray-500">Klik untuk upload file PDF surat kepemilikan</p>
                                <p class="text-xs text-gray-400 mt-1">Ukuran maksimal: 5MB</p>
                            </div>
                        </div>
                        <p v-if="form.errors.surat_kepemilikan" class="text-red-600 text-xs mt-1">{{ form.errors.surat_kepemilikan }}</p>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Pengajuan</span>
                        </button>
                        <Link :href="route('perizinan.index')" class="btn-secondary">
                            Batal
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Form pengajuan perizinan
const form = useForm({
    nama_pemohon:      '',
    nama_usaha:        '',
    lokasi_usaha:      '',
    omzet:             '',
    surat_kepemilikan: null,
});

const fileInput    = ref(null);
const selectedFileName = ref('');

// File Upload: handle perubahan file yang dipilih
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.surat_kepemilikan = file;
        selectedFileName.value = file.name;
    }
};

const submit = () => {
    form.post(route('perizinan.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout title="Pengaturan Akun">
        <div class="max-w-2xl space-y-6">
            <!-- Form update profil -->
            <div class="card">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Informasi Profil</h3>

                <form @submit.prevent="submitProfil" class="space-y-4">
                    <!-- Validasi Input Form: Nama -->
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input
                            v-model="profilForm.name"
                            type="text"
                            class="input-field"
                            :class="{ 'border-red-500': profilForm.errors.name }"
                        />
                        <p v-if="profilForm.errors.name" class="text-red-600 text-xs mt-1">{{ profilForm.errors.name }}</p>
                    </div>

                    <!-- Validasi Input Form: Email -->
                    <div>
                        <label class="form-label">Email</label>
                        <input
                            v-model="profilForm.email"
                            type="email"
                            class="input-field"
                            :class="{ 'border-red-500': profilForm.errors.email }"
                        />
                        <p v-if="profilForm.errors.email" class="text-red-600 text-xs mt-1">{{ profilForm.errors.email }}</p>
                    </div>

                    <div>
                        <label class="form-label">Role</label>
                        <input :value="roleLabel" type="text" class="input-field bg-gray-50" readonly />
                        <p class="text-gray-500 text-xs mt-1">Role tidak dapat diubah sendiri.</p>
                    </div>

                    <button type="submit" class="btn-primary" :disabled="profilForm.processing">
                        <span v-if="profilForm.processing">Menyimpan...</span>
                        <span v-else>Simpan Perubahan</span>
                    </button>
                </form>
            </div>

            <!-- Form ganti password -->
            <div class="card">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Ganti Password</h3>

                <form @submit.prevent="submitPassword" class="space-y-4">
                    <!-- Verifikasi password lama -->
                    <div>
                        <label class="form-label">Password Lama</label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            placeholder="Masukkan password lama"
                            class="input-field"
                            :class="{ 'border-red-500': passwordForm.errors.current_password }"
                        />
                        <p v-if="passwordForm.errors.current_password" class="text-red-600 text-xs mt-1">
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <!-- Validasi Password baru -->
                    <div>
                        <label class="form-label">Password Baru</label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            placeholder="Minimal 8 karakter"
                            class="input-field"
                            :class="{ 'border-red-500': passwordForm.errors.password }"
                        />
                        <p class="text-gray-500 text-xs mt-1">
                            Min 8 karakter, huruf besar, huruf kecil, dan angka.
                        </p>
                        <p v-if="passwordForm.errors.password" class="text-red-600 text-xs mt-1">
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <!-- Konfirmasi password baru -->
                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            placeholder="Ulangi password baru"
                            class="input-field"
                        />
                    </div>

                    <button type="submit" class="btn-primary" :disabled="passwordForm.processing">
                        <span v-if="passwordForm.processing">Menyimpan...</span>
                        <span v-else>Ganti Password</span>
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
});

// Form update profil
const profilForm = useForm({
    name:  props.user.name,
    email: props.user.email,
});

// Form ganti password
const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

const roleLabel = computed(() => {
    const labels = {
        'pengguna':    'Pengguna',
        'admin-pemda': 'Admin Pemerintah Daerah',
        'superadmin':  'Super Administrator',
    };
    return labels[props.user.role] || props.user.role;
});

const submitProfil = () => {
    profilForm.put(route('akun.update'));
};

const submitPassword = () => {
    passwordForm.put(route('akun.password'), {
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

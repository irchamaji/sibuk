<template>
    <AuthLayout>
        <!-- Card informasi akun demo (warna kuning audit) -->
        <div class="card-audit mb-4">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-amber-800" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-bold text-amber-900 text-sm mb-2">🔍 Instruksi Simulasi Audit Keamanan</p>
                    <p class="text-amber-900 text-xs mb-2">Gunakan akun berikut untuk simulasi:</p>
                    <div class="space-y-1 text-xs font-mono bg-black/10 rounded p-2">
                        <div>
                            <span class="font-semibold">Pengguna:</span>
                            pengguna@email.com / Passwrod123
                        </div>
                        <div>
                            <span class="font-semibold">Admin Pemda:</span>
                            admin@pemda.com / Passwrod123
                        </div>
                        <div>
                            <span class="font-semibold">Superadmin:</span>
                            superadmin@pemda.com / Passwrod123
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form login -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-6" style="color: #895129;">Masuk ke Akun</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Validasi Input Form: Email -->
                <div>
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        placeholder="nama@email.com"
                        class="input-field"
                        :class="{ 'border-red-500': errors.email }"
                    />
                    <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
                </div>

                <!-- Validasi Input Form: Password -->
                <div>
                    <label class="form-label">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input-field"
                        :class="{ 'border-red-500': errors.password }"
                    />
                    <p v-if="errors.password" class="text-red-600 text-xs mt-1">{{ errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div></div>
                    <Link :href="route('password.request')" class="text-sm hover:underline" style="color: #29638A;">
                        Lupa password?
                    </Link>
                </div>

                <button type="submit" class="btn-primary w-full" :disabled="form.processing">
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk</span>
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">
                Belum punya akun?
                <Link :href="route('register')" class="font-medium hover:underline" style="color: #895129;">
                    Daftar sekarang
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

// Manajemen Form Login
const form = useForm({
    email:    '',
    password: '',
});

// Errors dari server (Manajemen Login Gagal)
const errors = form.errors;

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

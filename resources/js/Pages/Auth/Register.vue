<template>
    <AuthLayout>
        <div class="card">
            <h2 class="text-xl font-semibold mb-6" style="color: #895129;">Buat Akun Baru</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Validasi Input Form: Nama -->
                <div>
                    <label class="form-label">Nama Lengkap</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Masukkan nama lengkap"
                        class="input-field"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-red-600 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <!-- Validasi Input Form: Email -->
                <div>
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="nama@email.com"
                        class="input-field"
                        :class="{ 'border-red-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <!-- Validasi Password: min 8, huruf besar+kecil, angka -->
                <div>
                    <label class="form-label">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Minimal 8 karakter"
                        class="input-field"
                        :class="{ 'border-red-500': form.errors.password }"
                    />
                    <p class="text-gray-500 text-xs mt-1">
                        Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka.
                    </p>
                    <p v-if="form.errors.password" class="text-red-600 text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="form-label">Konfirmasi Password</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Ulangi password"
                        class="input-field"
                        :class="{ 'border-red-500': form.errors.password_confirmation }"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-red-600 text-xs mt-1">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>

                <button type="submit" class="btn-primary w-full" :disabled="form.processing">
                    <span v-if="form.processing">Mendaftarkan...</span>
                    <span v-else>Daftar Sekarang</span>
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun?
                <Link :href="route('login')" class="font-medium hover:underline" style="color: #895129;">
                    Masuk
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

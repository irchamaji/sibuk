<template>
    <AuthLayout>
        <div class="card">
            <h2 class="text-xl font-semibold mb-2" style="color: #895129;">Lupa Password</h2>
            <p class="text-gray-500 text-sm mb-6">
                Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.
            </p>

            <form @submit.prevent="submit" class="space-y-4">
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

                <button type="submit" class="btn-primary w-full" :disabled="form.processing">
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirim Link Reset</span>
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">
                <Link :href="route('login')" class="font-medium hover:underline" style="color: #895129;">
                    ← Kembali ke halaman login
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

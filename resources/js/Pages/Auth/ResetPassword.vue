<template>
    <AuthLayout>
        <div class="card">
            <h2 class="text-xl font-semibold mb-6" style="color: #895129;">Reset Password</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Email tersembunyi dari URL -->
                <div>
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="input-field bg-gray-50"
                        readonly
                    />
                </div>

                <!-- Validasi Password: min 8 karakter -->
                <div>
                    <label class="form-label">Password Baru</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Minimal 8 karakter"
                        class="input-field"
                        :class="{ 'border-red-500': form.errors.password }"
                    />
                    <p v-if="form.errors.password" class="text-red-600 text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Ulangi password baru"
                        class="input-field"
                    />
                </div>

                <button type="submit" class="btn-primary w-full" :disabled="form.processing">
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Reset Password</span>
                </button>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token:                 props.token,
    email:                 props.email,
    password:              '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

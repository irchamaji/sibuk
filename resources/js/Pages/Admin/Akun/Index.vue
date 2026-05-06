<template>
    <AppLayout title="Manajemen Akun">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Manajemen Akun Pengguna</h3>
                    <p class="text-gray-500 text-sm">Tambah, edit, dan hapus akun pengguna sistem</p>
                </div>
                <button @click="showCreateModal = true" class="btn-primary text-sm">
                    + Tambah Akun
                </button>
            </div>

            <!-- Tabel daftar akun -->
            <div class="card p-0 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100" style="background-color: #FFE1CC;">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">ID</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Role</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide">Dibuat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ user.id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
                                         style="background-color: #FFE1CC; color: #895129;">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <span :class="roleBadgeClass(user.role)" class="text-xs px-2 py-1 rounded-full font-medium">
                                    {{ roleLabel(user.role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <button @click="editUser(user)"
                                            class="text-sm font-medium hover:underline"
                                            style="color: #29638A;">
                                        Edit
                                    </button>
                                    <button @click="openPasswordModal(user)"
                                            class="text-sm font-medium hover:underline"
                                            style="color: #895129;">
                                        Password
                                    </button>
                                    <button @click="confirmDelete(user)"
                                            class="text-sm font-medium text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah / Edit Akun -->
        <div v-if="showCreateModal || showEditModal"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h4 class="text-base font-semibold text-gray-800">
                        {{ showEditModal ? 'Edit Akun' : 'Tambah Akun Baru' }}
                    </h4>
                </div>
                <form @submit.prevent="showEditModal ? submitEdit() : submitCreate()" class="p-6 space-y-4">
                    <!-- Validasi Input Form: Nama -->
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input v-model="activeForm.name" type="text" class="input-field"
                               :class="{ 'border-red-500': activeForm.errors.name }" />
                        <p v-if="activeForm.errors.name" class="text-red-600 text-xs mt-1">{{ activeForm.errors.name }}</p>
                    </div>

                    <!-- Validasi Input Form: Email -->
                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="activeForm.email" type="email" class="input-field"
                               :class="{ 'border-red-500': activeForm.errors.email }" />
                        <p v-if="activeForm.errors.email" class="text-red-600 text-xs mt-1">{{ activeForm.errors.email }}</p>
                    </div>

                    <!-- Pilih Role (RBAC) -->
                    <div>
                        <label class="form-label">Role</label>
                        <select v-model="activeForm.role" class="input-field">
                            <option value="pengguna">Pengguna</option>
                            <option value="admin-pemda">Admin Pemda</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>

                    <!-- Password (hanya saat tambah) -->
                    <div v-if="showCreateModal">
                        <label class="form-label">Password</label>
                        <input v-model="createForm.password" type="password" class="input-field"
                               :class="{ 'border-red-500': createForm.errors.password }" />
                        <p class="text-gray-500 text-xs mt-1">Min 8 karakter, huruf besar, kecil, dan angka.</p>
                        <p v-if="createForm.errors.password" class="text-red-600 text-xs mt-1">{{ createForm.errors.password }}</p>
                    </div>
                    <div v-if="showCreateModal">
                        <label class="form-label">Konfirmasi Password</label>
                        <input v-model="createForm.password_confirmation" type="password" class="input-field" />
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <button type="submit" class="btn-primary" :disabled="activeForm.processing">
                            {{ showEditModal ? 'Simpan Perubahan' : 'Buat Akun' }}
                        </button>
                        <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ganti Password -->
        <div v-if="showPasswordModal"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h4 class="text-base font-semibold text-gray-800">Ganti Password</h4>
                    <p class="text-sm text-gray-500 mt-0.5">Akun: <strong>{{ passwordTarget?.name }}</strong></p>
                </div>
                <form @submit.prevent="submitPassword" class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Password Baru</label>
                        <input v-model="passwordForm.password" type="password" class="input-field"
                               :class="{ 'border-red-500': passwordForm.errors.password }" />
                        <p class="text-gray-500 text-xs mt-1">Min 8 karakter, huruf besar, kecil, dan angka.</p>
                        <p v-if="passwordForm.errors.password" class="text-red-600 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input v-model="passwordForm.password_confirmation" type="password" class="input-field"
                               :class="{ 'border-red-500': passwordForm.errors.password_confirmation }" />
                        <p v-if="passwordForm.errors.password_confirmation" class="text-red-600 text-xs mt-1">{{ passwordForm.errors.password_confirmation }}</p>
                    </div>
                    <div class="flex items-center space-x-3 pt-2">
                        <button type="submit" class="btn-primary" :disabled="passwordForm.processing">
                            Simpan Password
                        </button>
                        <button type="button" @click="closePasswordModal" class="btn-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Konfirmasi hapus -->
        <div v-if="deleteTarget"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-sm shadow-xl p-6">
                <h4 class="text-base font-semibold text-gray-800 mb-2">Hapus Akun?</h4>
                <p class="text-gray-500 text-sm mb-4">
                    Akun <strong>{{ deleteTarget.name }}</strong> ({{ deleteTarget.email }}) akan dihapus permanen.
                </p>
                <div class="flex items-center space-x-3">
                    <button @click="submitDelete" class="btn-danger">Hapus</button>
                    <button @click="deleteTarget = null" class="btn-secondary">Batal</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    users: Array,
});

// State modal
const showCreateModal  = ref(false);
const showEditModal    = ref(false);
const showPasswordModal = ref(false);
const deleteTarget     = ref(null);
const editingUser      = ref(null);
const passwordTarget   = ref(null);

// Form tambah akun baru
const createForm = useForm({
    name:                  '',
    email:                 '',
    role:                  'pengguna',
    password:              '',
    password_confirmation: '',
});

// Form edit akun
const editForm = useForm({
    name:  '',
    email: '',
    role:  'pengguna',
});

// Form ganti password
const passwordForm = useForm({
    password:              '',
    password_confirmation: '',
});

// Form aktif (tambah atau edit)
const activeForm = computed(() => showEditModal.value ? editForm : createForm);

// Buka modal edit dengan data user
const editUser = (user) => {
    editingUser.value = user;
    editForm.name  = user.name;
    editForm.email = user.email;
    editForm.role  = user.role;
    showEditModal.value = true;
};

// Tutup semua modal
const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value   = false;
    editingUser.value     = null;
    createForm.reset();
    editForm.reset();
};

// Submit tambah akun
const submitCreate = () => {
    createForm.post(route('admin.akun.store'), {
        onSuccess: closeModal,
    });
};

// Submit edit akun
const submitEdit = () => {
    editForm.put(route('admin.akun.update', editingUser.value.id), {
        onSuccess: closeModal,
    });
};

// Buka modal ganti password
const openPasswordModal = (user) => {
    passwordTarget.value = user;
    passwordForm.reset();
    showPasswordModal.value = true;
};

// Tutup modal password
const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordTarget.value = null;
    passwordForm.reset();
};

// Submit ganti password
const submitPassword = () => {
    passwordForm.put(route('admin.akun.password', passwordTarget.value.id), {
        onSuccess: closePasswordModal,
    });
};

// Konfirmasi hapus akun
const confirmDelete = (user) => {
    deleteTarget.value = user;
};

// Submit hapus akun
const submitDelete = () => {
    router.delete(route('admin.akun.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const roleLabel = (role) => {
    const labels = {
        'pengguna':    'Pengguna',
        'admin-pemda': 'Admin Pemda',
        'superadmin':  'Superadmin',
    };
    return labels[role] || role;
};

const roleBadgeClass = (role) => {
    const map = {
        'pengguna':    'bg-gray-100 text-gray-700',
        'admin-pemda': 'bg-blue-100 text-blue-700',
        'superadmin':  'bg-purple-100 text-purple-700',
    };
    return map[role] || 'bg-gray-100 text-gray-700';
};
</script>

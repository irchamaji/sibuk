<template>
    <!-- Layout halaman autentikasi (login, register, lupa password) -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
         style="background-color: #FFE1CC;">
        <div class="max-w-md w-full space-y-6">
            <!-- Logo dan judul aplikasi -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4"
                     style="background-color: #895129;">
                    <span class="text-white text-2xl font-bold">S</span>
                </div>
                <h1 class="text-3xl font-bold" style="color: #895129;">SIBUK</h1>
                <p class="text-sm text-gray-600 mt-1">Sistem Informasi Buka Usaha Kecil</p>
                <p class="text-xs text-gray-500">Pemerintah Daerah Kota Contoh</p>
            </div>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success"
                 class="bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error"
                 class="bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ $page.props.flash.error }}
            </div>

            <!-- Konten halaman -->
            <slot />
        </div>
    </div>
</template>

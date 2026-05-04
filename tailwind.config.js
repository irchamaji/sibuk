/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                // Warna tema utama SIBUK
                primary: '#895129',   // coklat - warna utama, button, header
                light:   '#FFE1CC',   // peach - background card, input bg
                success: '#298A72',   // hijau - status Diizinkan, success
                info:    '#29638A',   // biru - link, secondary action, status Pengajuan
                audit:   '#E4D00A',   // kuning - card informasi audit, akun demo
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};

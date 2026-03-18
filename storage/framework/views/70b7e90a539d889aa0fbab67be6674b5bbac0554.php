
<style>[x-cloak] { display: none !important; }</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<?php if(file_exists(public_path('build/manifest.json'))): ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard-layout.css')); ?>">
<?php else: ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/ultra-ui.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/ultra-premium.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard-layout.css')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa',
                            500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a',
                        },
                        accent: { red: '#dc2626', 'red-dark': '#b91c1c' },
                        violet: { 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9' },
                        neon: {
                            blue: '#00d4ff',
                            purple: '#b794f6',
                            pink: '#ff6b9d',
                            orange: '#ff8c42',
                            green: '#00ff88',
                        },
                        'ultra-bg': { dark: '#0a0e27', 'dark-secondary': '#141b3d' },
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
                        'card-hover': '0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.06)',
                        'elevated': '0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04)',
                        'nav': '0 -4px 24px -4px rgb(0 0 0 / 0.08)',
                    },
                    borderRadius: { '2xl': '1rem', '3xl': '1.25rem' },
                    fontFamily: { sans: ['Poppins', 'Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                }
            }
        }
    </script>
<?php endif; ?>
<?php /**PATH /Users/sabri/Documents/ERP Unions V2/resources/views/partials/head-assets.blade.php ENDPATH**/ ?>
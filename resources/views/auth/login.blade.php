<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Soluciones del Agua</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#005293',
                            light: '#22B0EA'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans antialiased text-gray-800">

    <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-brand-blue p-6 text-center flex flex-col items-center">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white mb-3 overflow-hidden p-0.5 shadow-md border border-white/20">
                <img src="/logo.jpg" alt="Logo Soluciones del Agua" class="w-full h-full object-cover rounded-full scale-[1.10]" onerror="this.style.display='none'; document.getElementById('water-fallback').style.display='inline-flex';">
                <span id="water-fallback" class="hidden items-center justify-center text-4xl text-brand-blue"><i class="fa-solid fa-water"></i></span>
            </div>
            <h1 class="text-2xl font-bold text-white">Soluciones del Agua</h1>
            <p class="text-brand-light text-sm mt-1">Sistema Administrativo y ERP</p>
        </div>
        
        <div class="p-8">
            <form method="POST" action="/login">
                @csrf
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required
                               value="admin@solucionesdelagua.com"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-light focus:border-transparent transition-shadow"
                               placeholder="admin@solucionesdelagua.com">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               value="password"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-light focus:border-transparent transition-shadow"
                               placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-brand-light hover:bg-brand-blue text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Iniciar Sesión
                </button>
            </form>
        </div>
        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Soluciones del Agua. Todos los derechos reservados.
        </div>
    </div>

</body>
</html>

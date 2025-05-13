<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>Login | Samoedra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredericka+the+Great&family=Fredoka:wght@300..700&family=Fuzzy+Bubbles:wght@400;700&family=Onest:wght@100..900&display=swap" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 min-h-screen flex items-center justify-center p-4 overflow-hidden" style="font-family: 'Fredoka', sans-serif;">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-4 -left-4 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob animation-delay-4000"></div>
    </div>

    <div class="w-full max-w-md">
        <!-- Card Login dengan efek glassmorphism -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl p-8 mx-4 border border-white/20">

            <!-- Logo dengan animasi hover -->
            <div class="text-center mb-8 transform transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('assets/samoedralogo.png') }}" alt="Samoedra Logo" class="mx-auto w-48">
            </div>

            <h1 class="text-2xl font-semibold text-center mb-4 text-gray-800">Login Admin</h1>

    <!-- Session Status -->
            @if (session('status'))
            <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded-lg">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm">
        @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="pl-11 w-full h-12 bg-gray-50/50 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:ring-2 focus:bg-white transition-all duration-200"
                            placeholder="Enter your email">
                    </div>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                    @enderror
        </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="pl-11 pr-11 w-full h-12 bg-gray-50/50 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:ring-2 focus:bg-white transition-all duration-200"
                            placeholder="Enter your password">
                        <button type="button" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 transition-colors"
                            onclick="togglePassword()">
                            <i id="togglePassword" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                    @enderror
        </div>

        <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember"
                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full h-12 flex justify-center items-center rounded-xl text-white text-lg font-normal bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 transform transition-all duration-200 hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} Samoedra. All rights reserved.
        </p>
        </div>

    <style>
        /* Tambahkan animasi gradient background */
        body {
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animasi blob background */
        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>

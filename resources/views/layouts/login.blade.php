<!DOCTYPE html>
<html lang="en">
@include('template.head')

<body class="login flex justify-center items-center min-h-screen backdrop-blur-sm">
    <div class="w-120 mx-auto p-4">
        <form action="/login" method="POST" class="bg-white p-6 rounded shadow-md">
            @csrf
            <img src="{{ asset('storage/images/LISO_LogoColored.png') }}" alt="Library Logo"
                class="h-40 w-40 mx-auto mb-4">
            <h1 class="text-2xl font-bold mb-6 text-center">Library Entrance</h1>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="email" name="email" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded">
            </div>
            <button type="submit" class="w-full bg-[#009639] text-white p-2 rounded">Login</button>
        </form>
    </div>
    @include('template.toast')
</body>

</html>

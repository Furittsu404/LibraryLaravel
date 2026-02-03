<!DOCTYPE html>
<html lang="en">
@include('template.head')

<body class="flex min-h-screen bg-white dark:bg-gray-900 transition-colors duration-200">
    @livewire('Sidebar')
    <div id="adminContent" class="flex-1 transition-all duration-300">
        {{ $slot }}
    </div>
    @include('template.toast')

    <script>
        // Set initial margin based on sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const adminContent = document.getElementById('adminContent');
            if (adminContent) {
                if (sidebarCollapsed) {
                    adminContent.classList.add('ml-16');
                } else {
                    adminContent.classList.add('ml-64');
                }
            }
        });
    </script>
</body>

</html>

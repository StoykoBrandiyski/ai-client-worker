@if(session('success'))
    <div id="alert-success" class="fixed top-5 right-5 z-50 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 shadow-lg" role="alert">
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414Z"/></svg>
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    </div>

    <script>
        // Auto-hide after 3 seconds
        setTimeout(() => {
            const alert = document.getElementById('alert-success');
            if (alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endif

@if(session('error'))
    <div class="fixed top-5 right-5 z-50 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 shadow-lg">
        {{ session('error') }}
    </div>
@endif

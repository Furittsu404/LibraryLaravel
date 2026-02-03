<?php
$pageName = $message;
?>
<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/head.php'; ?>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full flex flex-col item-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4"><?= htmlspecialchars($message) ?></h1>
        <p class="text-gray-700">You can return to the homepage <a href="{{ route('scanner.index') }}"
                class="text-green-600 underline">here</a>.
        </p>
    </div>
</body>

</html>

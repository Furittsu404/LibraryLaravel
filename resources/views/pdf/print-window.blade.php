<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #525659;
        }

        iframe {
            border: none;
            width: 100%;
            height: 100vh;
        }

        #loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            font-family: Arial, sans-serif;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #009639;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="loading">
        <div class="spinner"></div>
        <p>Preparing print dialog...</p>
    </div>

    <iframe id="pdfFrame" style="display: none;"></iframe>

    <script>
        // Convert base64 to blob
        function base64ToBlob(base64, type = 'application/pdf') {
            const binStr = atob(base64);
            const len = binStr.length;
            const arr = new Uint8Array(len);
            for (let i = 0; i < len; i++) {
                arr[i] = binStr.charCodeAt(i);
            }
            return new Blob([arr], {
                type: type
            });
        }

        // Load PDF and trigger print
        window.onload = function() {
            const pdfData = '{{ $pdfData }}';
            const blob = base64ToBlob(pdfData);
            const url = URL.createObjectURL(blob);

            const iframe = document.getElementById('pdfFrame');
            iframe.src = url;

            // Hide loading and show iframe
            document.getElementById('loading').style.display = 'none';
            iframe.style.display = 'block';

            // Wait for iframe to load, then trigger print
            iframe.onload = function() {
                // Small delay to ensure PDF is fully rendered
                setTimeout(function() {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    // Optional: Close window after print dialog is dismissed
                    // Uncomment if you want the window to close automatically
                    // setTimeout(function() {
                    //     window.close();
                    // }, 1000);
                }, 500);
            };
        };
    </script>
</body>

</html>

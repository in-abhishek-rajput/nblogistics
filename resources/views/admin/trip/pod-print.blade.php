<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POD Receipt - {{ $trip->lr_number ?? 'N/A' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .img-container {
            max-width: 100%;
            text-align: center;
        }
        .img-container img {
            max-width: 100%;
            height: auto;
            max-height: 98vh; /* Leave a small margin */
            object-fit: contain;
        }
        @media print {
            body {
                background-color: white;
                min-height: auto;
            }
            @page {
                size: auto;
                margin: 0;
            }
            .img-container img {
                max-width: 100%;
                max-height: 99vh;
                page-break-inside: avoid;
                display: block;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>

<div class="img-container">
    <img src="{{ asset('storage/' . $trip->pod_receipt) }}" alt="POD Receipt">
</div>

@if(!empty($autoPrint))
<script>
    window.addEventListener('load', function () {
        setTimeout(function() {
            window.print();
        }, 500); // slight delay to ensure image renders before print dialog opens
    });
</script>
@endif

</body>
</html>

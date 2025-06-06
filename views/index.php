<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Upload</title>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Transaction file uploaded successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            $error = match ($_GET['error']) {
                '1' => 'Failed to process transactions.',
                '2' => 'Failed to upload file.',
                default => 'An unknown error occurred.'
            };
            echo $error;
            ?>
        </div>
    <?php endif; ?>

    <h1>Upload Transactions</h1>

    <form action="/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="transaction" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>
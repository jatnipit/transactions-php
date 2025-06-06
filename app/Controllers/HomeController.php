<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Services\TransactionService;
use App\View;

class HomeController
{
    private TransactionService $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService(new TransactionModel());
    }

    public function index(): View
    {
        return View::make('index');
    }

    public function transactions(): View
    {
        $model = new TransactionModel();
        $transactions = $model->selectTransactions();
        $totals = $this->transactionService->getTotal($transactions);

        return View::make('transactions', ['transactions' => $transactions, 'totals' => $totals]);
    }

    public function upload()
    {
        try {
            $this->transactionService->validateUploadedFile($_FILES['transaction']);

            $filePath = STORAGE_PATH . '/' . $_FILES['transaction']['name'];

            if (!move_uploaded_file($_FILES['transaction']['tmp_name'], $filePath)) {
                throw new \RuntimeException('Failed to move uploaded file');
            }

            if ($this->transactionService->processTransactionFile($filePath)) {
                header('Location: /transactions');
            } else {
                header("Location: /?error=1");
            }
        } catch (\Exception $e) {
            error_log("Upload failed: " . $e->getMessage());
            header('Location: /?error=2');
        }
        exit;
    }
}

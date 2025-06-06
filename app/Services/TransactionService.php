<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TransactionModel;

class TransactionService
{
    private TransactionModel $transactionModel;

    public function __construct(TransactionModel $transactionModel)
    {
        $this->transactionModel = $transactionModel;
    }

    public function processTransactionFile(string $filePath): bool
    {
        if (!$this->validateFile($filePath)) {
            throw new \RuntimeException('Invalid file format');
        }

        $transactions = $this->parseTransactions($filePath);
        return $this->transactionModel->insertManyTransactions($transactions);
    }

    public function validateUploadedFile(array $file): void
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload failed with error code: ' . $file['error']);
        }

        if ($file['type'] !== 'text/csv') {
            throw new \RuntimeException('Invalid file type. Only CSV files are allowed.');
        }
    }

    private function validateFile(string $filePath): bool
    {
        return pathinfo($filePath, PATHINFO_EXTENSION) === 'csv';
    }

    public function getTransactionFiles(): array
    {
        $dirPath = STORAGE_PATH;
        $files = [];

        foreach (scandir($dirPath) as $file) {
            if ($file === '.' || $file === '..' || is_dir($dirPath . DIRECTORY_SEPARATOR . $file)) {
                continue;
            }
            $files[] = $dirPath . DIRECTORY_SEPARATOR . $file;
        }

        return $files;
    }

    private function parseTransactions(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File {$filePath} doesn't exist.");
        }

        $transactions = [];
        $handle = fopen($filePath, 'r');

        fgetcsv($handle); // Skip header

        while (($data = fgetcsv($handle)) !== false) {
            $transactions[] = $this->formatTransaction($data);
        }

        fclose($handle);
        return $transactions;
    }

    private function formatTransaction(array $data): array
    {
        return [
            'date' => $data[0],
            'checkNumber' => $data[1],
            'description' => $data[2],
            'amount' => (float) str_replace(['$', ','], '', $data[3])
        ];
    }

    public function getTotal(array $transactions): array
    {
        $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

        foreach ($transactions as $transaction) {
            $totals['netTotal'] += $transaction['amount'];
            if ($transaction['amount'] >= 0) {
                $totals['totalIncome'] += $transaction['amount'];
            } else {
                $totals['totalExpense'] += $transaction['amount'];
            }
        }

        return $totals;
    }
}

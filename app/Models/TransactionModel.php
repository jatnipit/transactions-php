<?php

declare(strict_types=1);

namespace App\Models;

class TransactionModel extends \App\Model
{
    public function insertTransaction(array $transaction)
    {
        $query = "INSERT INTO transactions (date, check_number, description, amount) 
                  VALUES (:date, :check_number, :description, :amount)";

        $stmt = $this->db->prepare($query);

        $formatDate = date('Y-m-d', strtotime($transaction['date']));

        return $stmt->execute([
            ':date' => $formatDate,
            ':check_number' => $transaction['checkNumber'],
            ':description' => $transaction['description'],
            ':amount' => $transaction['amount']
        ]);
    }

    public function insertManyTransactions(array $transactions)
    {
        $this->db->beginTransaction();

        try {
            foreach ($transactions as $transaction) {
                $this->insertTransaction($transaction);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo $e->getMessage();
            return false;
        }
    }

    public function selectTransactions(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions ORDER BY date ASC");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TransactionService
{
    public function rankTransactionsByMerchantUser(): Collection
    {
        // Ambil data transaksi dari database billiton
        $transactions = DB::connection('pgsql_billiton')->table('transaction')
            ->select('user_uid', DB::raw('COUNT(transaction_id) as transaction_count'))
            ->groupBy('user_uid')
            ->get(); // Get as collection

        // Ambil data merchant dari database sld_id_cr
        $merchants = DB::connection('pgsql')->table('merchants')
            ->select('user_id', 'name')
            ->get()
            ->keyBy('user_id'); // Menggunakan keyBy untuk mempermudah pencocokan

        // Gabungkan data transaksi dengan data merchant
        $rankedTransactions = $transactions->map(function ($transaction) use ($merchants) {
            $user_id = $transaction->user_uid;
            $merchant = $merchants->get($user_id);

            return (object)[
                'user_uid' => $user_id,
                'transaction_count' => $transaction->transaction_count,
                'merchant_name' => $merchant ? $merchant->name : 'Unknown',
            ];
        });

        // Urutkan hasil berdasarkan jumlah transaksi
        $rankedTransactions = $rankedTransactions->sortByDesc('transaction_count');

        return $rankedTransactions;
    }
}

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
            ->select('account_number', DB::raw('COUNT(transaction_code) as transaction_count'))
            ->groupBy('account_number')
            ->get(); // Get as collection

        // Ambil data merchant dari database sld_id_cr
        $merchants = DB::connection('pgsql')->table('merchants')
            ->select('no', 'user_id', 'name', 'address', 'email', 'mid') // Pastikan kolom tambahan diambil
            ->get()
            ->keyBy('no'); // Menggunakan keyBy untuk mempermudah pencocokan

        // Ambil data user dari database sld_id_cr
        $users = DB::connection('pgsql')->table('users')
            ->select('id', 'username')
            ->get()
            ->keyBy('id'); // Menggunakan keyBy untuk mempermudah pencocokan

        // Gabungkan data transaksi dengan data merchant dan user
        $rankedTransactions = $transactions->map(function ($transaction) use ($merchants, $users) {
            $account_number = $transaction->account_number;
            $merchant = $merchants->get($account_number);

            // Default values
            $merchant_name = 'Unknown';
            $address = 'Unknown';
            $email = 'Unknown';
            $mid = 'Unknown';
            $username = 'Unknown';

            if ($merchant) {
                $merchant_name = $merchant->name; // Akses 'name' jika ada
                $address = $merchant->address; // Akses 'address' jika ada
                $email = $merchant->email; // Akses 'email' jika ada
                $mid = $merchant->mid; // Akses 'mid' jika ada
                // Ambil data user berdasarkan user_id dari merchant
                $user = $users->get($merchant->user_id);
                if ($user) {
                    $username = $user->username;
                }
            }

            return (object)[
                'account_number' => $account_number,
                'merchant_name' => $merchant_name,
                'address' => $address,
                'email' => $email,
                'mid' => $mid,
                'username' => $username,
                'transaction_count' => $transaction->transaction_count,
            ];
        });

        // Urutkan hasil berdasarkan jumlah transaksi
        $rankedTransactions = $rankedTransactions->sortByDesc('transaction_count');

        return $rankedTransactions;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    /**
     * Display trial balance grouped by account.
     */
    public function index(): JsonResponse
    {
        $trialBalance = Account::select(
            'accounts.id',
            'accounts.code',
            'accounts.name',
            'accounts.type',
            DB::raw('COALESCE(SUM(CASE WHEN journal_entry_lines.type = "debit" THEN journal_entry_lines.amount ELSE 0 END), 0) as total_debits'),
            DB::raw('COALESCE(SUM(CASE WHEN journal_entry_lines.type = "credit" THEN journal_entry_lines.amount ELSE 0 END), 0) as total_credits')
        )
        ->leftJoin('journal_entry_lines', 'accounts.id', '=', 'journal_entry_lines.account_id')
        ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
        ->orderBy('accounts.code')
        ->get()
        ->map(function ($account) {
            $balance = $account->total_debits - $account->total_credits;
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'total_debits' => (float) $account->total_debits,
                'total_credits' => (float) $account->total_credits,
                'balance' => (float) $balance,
            ];
        });

        $totalDebits = $trialBalance->sum('total_debits');
        $totalCredits = $trialBalance->sum('total_credits');

        return response()->json([
            'accounts' => $trialBalance,
            'totals' => [
                'total_debits' => (float) $totalDebits,
                'total_credits' => (float) $totalCredits,
                'difference' => (float) ($totalDebits - $totalCredits),
            ],
        ]);
    }
}

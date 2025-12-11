<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $entries = JournalEntry::with('lines.account')
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        return response()->json($entries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate double-entry: total debits must equal total credits
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($request->lines as $line) {
            if ($line['type'] === 'debit') {
                $totalDebits += $line['amount'];
            } else {
                $totalCredits += $line['amount'];
            }
        }

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return response()->json([
                'errors' => [
                    'lines' => ['Total debits must equal total credits. Debits: ' . number_format($totalDebits, 2) . ', Credits: ' . number_format($totalCredits, 2)]
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $entry = JournalEntry::create([
                'entry_date' => $request->entry_date,
                'description' => $request->description,
                'reference_number' => $request->reference_number,
            ]);

            foreach ($request->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            DB::commit();

            $entry->load('lines.account');
            return response()->json($entry, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create journal entry: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $entry = JournalEntry::with('lines.account')->findOrFail($id);
        return response()->json($entry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $entry = JournalEntry::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate double-entry
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($request->lines as $line) {
            if ($line['type'] === 'debit') {
                $totalDebits += $line['amount'];
            } else {
                $totalCredits += $line['amount'];
            }
        }

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return response()->json([
                'errors' => [
                    'lines' => ['Total debits must equal total credits. Debits: ' . number_format($totalDebits, 2) . ', Credits: ' . number_format($totalCredits, 2)]
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $entry->update([
                'entry_date' => $request->entry_date,
                'description' => $request->description,
                'reference_number' => $request->reference_number,
            ]);

            // Delete existing lines
            $entry->lines()->delete();

            // Create new lines
            foreach ($request->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            DB::commit();

            $entry->load('lines.account');
            return response()->json($entry);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update journal entry: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $entry = JournalEntry::findOrFail($id);
        $entry->delete();
        return response()->json(['message' => 'Journal entry deleted successfully']);
    }
}

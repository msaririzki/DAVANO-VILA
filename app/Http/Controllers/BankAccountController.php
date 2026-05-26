<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $bankAccount = BankAccount::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::record(
            $request,
            'bank_account.created',
            'Menambahkan rekening '.$bankAccount->bank_name.' '.$bankAccount->account_number,
            $bankAccount,
            null,
            $bankAccount->only(['bank_name', 'account_number', 'account_name', 'is_active']),
        );

        return redirect()
            ->route('admin.web-settings')
            ->with('status', 'Rekening bank berhasil ditambahkan.');
    }

    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $oldValues = $bankAccount->only(['bank_name', 'account_number', 'account_name', 'is_active']);

        $bankAccount->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::record(
            $request,
            'bank_account.updated',
            'Mengubah rekening '.$bankAccount->bank_name.' '.$bankAccount->account_number,
            $bankAccount,
            $oldValues,
            $bankAccount->only(['bank_name', 'account_number', 'account_name', 'is_active']),
        );

        return redirect()
            ->route('admin.web-settings')
            ->with('status', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * @return array<string, string>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'bank_name' => ['required', 'string', 'max:80'],
            'account_number' => ['required', 'string', 'max:80', 'regex:/^[0-9 .-]+$/'],
            'account_name' => ['required', 'string', 'max:160'],
        ]);
    }
}

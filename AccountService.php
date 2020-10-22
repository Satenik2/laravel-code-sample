<?php

namespace App\Services;

use App\Models\Account;

class AccountService
{
    public function getAccount($id)
    {
        $account = Account::where('created_by_id', auth()->user()->id)->find($id);
        if (is_null($account)) {
            throw new \Exception('Something went wrong');
        }
        return $account;
    }

    public function getOpenInvoicesCount($account)
    {
        return $account->loadCount(['invoices' => function ($q) {
            $q->whereIn('status', ['Draft', 'Ready', 'Active', 'Approved']);
        }]);

    }

    public function getTask($accountId, $taskId)
    {
        $account = $this->getAccount($accountId);
        $task = $account->task()->where('id', $taskId);
        if (is_null($task)) {
            throw new \Exception('Something went wrong');
        }
        return $task;
    }

    public function destroy($account)
    {
        $destroy = $account->delete();
        if (is_null($destroy)) {
            throw new \Exception('Cant delete Lead item');
        }
        return $destroy;
    }
}

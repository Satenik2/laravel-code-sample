<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Account\CreateAccountRequest;
use App\Models\Account;
use App\Services\GuzzleHttpService;
use App\Services\AccountService;
use App\Services\GlobalsService;
use App\Services\TaskService;
use App\Transformers\AccountListTransformer;
use App\Transformers\AccountItemTransformer;
use App\Transformers\AccountOpportunityTransformer;
use App\Transformers\CasesListTransformer;
use App\Transformers\ContactListTransformer;
use App\Transformers\InvoiceListTransformer;
use App\Transformers\OrderListTransformer;
use App\Transformers\QuoteListTransformer;
use App\Transformers\TaskListTransformer;
use Illuminate\Http\Request;
use Exception;

class AccountController extends BaseController
{

    private $service;
    private $authUser;
    private $globalService;
    private $guzzleHttpService;
    private $taskService;

    public function __construct(AccountService $accountService, GlobalsService $globalService, GuzzleHttpService $guzzleHttpService, TaskService $taskService)
    {
        $this->service = $accountService;
        $this->authUser = \Auth::user();
        $this->globalService = $globalService;
        $this->guzzleHttpService = $guzzleHttpService;
        $this->taskService = $taskService;
    }

    /**
     * @param $skip
     * @param $take
     *
     * @return mixed
     */
    public function index($skip, $take)
    {
        try {
            startLog(__METHOD__);
            $data = Account::skip($skip)
                            ->take($take)
                            ->where('created_by_id', $this->authUser->id)
                            ->with('emailAddresses:name')
                            ->with('assignedUser:id,first_name,last_name')
                            ->with('phoneNumbers:numeric,name,type')
                            ->withCount('opportunities')
                            ->orderBy('modified_at', 'desc')
                            ->get();
            successLog(__METHOD__);

            return $this->response->collection($data, new AccountListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getAccount($id)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($id);
            $account = $this->service->getOpenInvoicesCount($account);
            successLog(__METHOD__);

            return $this->response->item($account, new AccountItemTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param CreateAccountRequest $request
     *
     * @return mixed
     */
    public function create(CreateAccountRequest $request)
    {
        try {
            startLog(__METHOD__);
            $data = $request->all();
            $createdAccount = $this->guzzleHttpService->HttpRequest('POST', 'Account', $data);
            successLog(__METHOD__);

            return [
                'message' => 'Account successfully created',
                'id' => $createdAccount->id,
            ];
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try {
            startLog(__METHOD__);
            $data = $request->all();
            $this->guzzleHttpService->HttpRequest('PUT', "Account/$id", $data);
            successLog(__METHOD__);

            return [
                'message' => 'Account successfully updated',
            ];
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($id);
            $destroy = $this->service->destroy($account);
            successLog(__METHOD__);

            return collect($destroy);
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getOpportunities($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->opportunities, new AccountOpportunityTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getQuotes($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->quotes, new QuoteListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getCases($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->cases, new CasesListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getContacts($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->contacts, new ContactListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getInvoices($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->invoices, new InvoiceListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getTasks($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->tasks, new TaskListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $taskId
     * @param $accountId
     * @param Request $request
     *
     * @return mixed
     */
    public function changeTaskStatus($accountId, $taskId, Request $request)
    {
        try {
            startLog(__METHOD__);
            $type = $request->get('type');
            $task = $this->service->getTask($accountId, $taskId);
            $this->taskService->changeStatus($task, $type);
            successLog(__METHOD__);

            return [
                'message' => 'Task successfully updated',
            ];
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }

    /**
     * @param $accountId
     *
     * @return mixed
     */
    public function getOrders($accountId)
    {
        try {
            startLog(__METHOD__);
            $account = $this->service->getAccount($accountId);
            successLog(__METHOD__);

            return $this->response->collection($account->orders, new OrderListTransformer());
        } catch (Exception $e) {
            errorLog(__METHOD__, $e);
            return $this->response->errorBadRequest($e->getMessage());
        }
    }
}

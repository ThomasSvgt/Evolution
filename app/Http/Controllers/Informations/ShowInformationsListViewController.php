<?php

namespace App\Http\Controllers\Informations;

use App\Facades\Pagination;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ShowInformationsListViewController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('informations/InformationsList', [
            'datatable' => fn() => $this->getInformations($request),
            'userList' => fn() => $this->userList(),
        ]);
    }

    private function getInformations(Request $request): object
    {
        $input = Validator::validate($request->all(), [
            'search' => ['sometimes', 'nullable', 'string'],
            'filters' => ['sometimes', 'nullable', 'array'],
        ]);

        $search = $input['search'] ?? null;
        $active = $input['filters']['status']['value'] ?? null;

        $query =  DB::table('informations as i')
            ->join('users', 'i.user_id', '=','users.id')
            ->whereSearch($search, ['i.adress', 'i.city', 'i.zip_code', 'i.phone'])
            ->select([
                'i.id as informationsId',
                'adress',
                'city',
                'zip_code',
                'phone',
                'users.id as userId',
                'users.name',
                'users.email',
            ]);
        return Pagination::get($request, $query);
    }


    public function userList(): object
    {

       return DB::table('users')
            ->select([
                'id',
                'name',
                'username',
                'email',
                'active',
                DB::raw('CONCAT(name, " - ", email) AS name_email')
            ])
           ->get();
    }
}

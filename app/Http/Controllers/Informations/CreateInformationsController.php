<?php

namespace App\Http\Controllers\Informations;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInformations;
use App\Http\Requests\CreateUser;
use App\Models\Informations;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class CreateInformationsController extends Controller
{
    /**
     * @param CreateInformations $request
     * @param Informations $informations
     * @return RedirectResponse
     */
    public function __invoke(CreateInformations $request, Informations $informations): RedirectResponse
    {
        $input = $request->validated();
        $input['adress'] = $request->adress;
        $input['city'] = $request->city;
        $input['phone'] = $request->phone;
        $input['zip_code'] = $request->zip_code;
        $input['user_id'] = $request->user_id['id'];

        Informations::create($input);

        return redirect()->back();
    }
}

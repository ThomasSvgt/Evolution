<?php

namespace App\Http\Controllers\Informations;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditInformations;
use App\Http\Requests\EditUser;
use App\Models\Informations;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class EditInformationsController extends Controller
{
    /**
     * @param EditInformations $request
     * @param Informations $informartions
     * @return RedirectResponse
     */
    public function __invoke(EditInformations $request, Informations $informartions): RedirectResponse
    {
        $input = $request->validated();
        $input['adress'] = $request->adress;
        $input['city'] = $request->city;
        $input['phone'] = $request->phone;
        $input['zip_code'] = $request->zip_code;
        $input['user_id'] = $request->user_id['id'];

        $informations = Informations::findOrFail($request->informationsId); // Supposons que vous avez un champ informations_id dans votre formulaire
        $informations->update($input);

        return redirect()->back();
    }

}

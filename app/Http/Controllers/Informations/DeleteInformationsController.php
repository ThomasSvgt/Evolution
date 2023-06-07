<?php

namespace App\Http\Controllers\Informations;

use App\Http\Controllers\Controller;
use App\Models\Informations;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteInformationsController extends Controller
{
    /**
     * @param Request $request
     * @param Informations $informations
     * @return RedirectResponse
     */
    public function __invoke(Request $request, Informations $informations): RedirectResponse
    {
        $informations->delete();

        return redirect()->back();
    }

}

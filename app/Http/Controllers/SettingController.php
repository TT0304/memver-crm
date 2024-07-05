<?php

namespace App\Http\Controllers;
use Gate;
use Symfony\Component\HttpFoundation\Response;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('settings.index');
    }
}
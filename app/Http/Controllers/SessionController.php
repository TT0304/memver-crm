<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('dashboards.index');
        } else {
            if (strpos(url()->previous(), 'admin') !== false) {
                $intendedUrl = url()->previous();
            } else {
                $intendedUrl = route('dashboards.index');
            }

            session()->put('url.intended', $intendedUrl);            
            return Inertia::render('auth-pages/signin/basic');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = auth()->user();
        session(['client_id' => $user->client_id]);

        if (! auth()->attempt(request(['email', 'password']), request('remember'))) {
            session()->flash('error', trans('app.sessions.login.login-error'));

            return redirect()->back();
        }

        if (auth()->user()->status == 0) {
            session()->flash('warning', trans('app.sessions.login.activate-warning'));

            auth()->logout();

            return redirect()->route('login');
        }

        return redirect()->intended(route('dashboard.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->logout();

        return redirect()->route('login');
    }
}
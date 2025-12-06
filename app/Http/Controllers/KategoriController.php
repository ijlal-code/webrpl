<?php

namespace App\Http\Controllers;

class KategoriController extends Controller
{
    private function featureDisabled()
    {
        abort(404, 'Fitur kategori arsip telah dinonaktifkan.');
    }

    public function index()
    {
        return $this->featureDisabled();
    }

    public function create()
    {
        return $this->featureDisabled();
    }

    public function store()
    {
        return $this->featureDisabled();
    }

    public function edit()
    {
        return $this->featureDisabled();
    }

    public function update()
    {
        return $this->featureDisabled();
    }

    public function destroy()
    {
        return $this->featureDisabled();
    }
}

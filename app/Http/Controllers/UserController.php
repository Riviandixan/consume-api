<?php

namespace App\Http\Controllers;

use App\Http\Libraries\BaseApi;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = (new BaseApi)->index('/user');
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // buat variable baru untuk menset parameter agar seusai dengan documentasi
        $payload = [
            'firstName' => $request->input('nama_depan'),
            'lastName' => $request->input('nama_belakang'),
            'email' => $request->input('email'),
        ];
        $baseApi = new BaseApi;
        $response = $baseApi->create('/user/create', $payload);

        // handdle juka request api nya gagal
        // di blade nanti bisa ditambahkan toast alert
        if ($response->failed()) {
            // $response->json agar response dari API bisa di akses sebagai array
        $errors = $response->json('data');

        $messages = "<ul>";

        foreach ($errors as $key => $msg) {
            $messages .= "<li>$key : $msg</li>";
        }

        $messages .= "</ul>";

        return redirect()->back()->with('message', "Data gagal disimpan $messages");
        }

        return redirect('/users')->with('message', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //kalian bisa coba untuk dd($response) untuk test apakah api nya sudah benar atau belum
        //sesuai documentasi api detail user akan menshow data detail seperti `email` yg tidak dimunculkan di api list index
        $response = (new BaseApi)->detail('/user', $id);
        return view('user.edit')->with([
            'user' => $response->json()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payload = [
            'firstName' => $request->input('nama_depan'),
            'lastName' => $request->input('nama_belakang'),
        ];

        $response = (new BaseApi)->update('/user', $id, $payload);
        if($response->failed()){
            $errors = $response->json('data');

            $messages = "<ul>";

            foreach($errors as $key => $msg){
                $messages .= "<li>$key : $msg</li>";
            }

            $messages .= "</ul>";

            return redirect('users')->with('message', "Data gagal diperbaharui $messages");
        }
        return redirect('users')->with('message', "Data berhasil diperbaharui");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = (new BaseApi)->delete('/user', $id);

        if ($response->failed()) {
            return redirect('users')->with('message', 'Data gagal dihapus');
        }

        return redirect('users')->with('message', 'Data berhasil dihapus');
    }
}

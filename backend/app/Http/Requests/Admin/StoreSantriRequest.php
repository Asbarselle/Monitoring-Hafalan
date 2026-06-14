<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $nama
 * @property string $nis
 * @property string|null $tempat_lahir
 * @property \Illuminate\Foundation\
 *         Support\Carbon $tanggal_lahir
 * @property string|null $jenis_kelamin
 * @property string|null $alamat
 * @property string|null $no_hp
 * @property \Illuminate\Http\UploadedFile|null $foto
 * @property int|null $orang_tua_id
 */
class StoreSantriRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:santri,nis',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'orang_tua_id' => 'nullable|exists:users,id',
        ];
    }
}

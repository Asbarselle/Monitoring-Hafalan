<?php

namespace App\Http\Requests\Ustadz;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $santri_id
 * @property int|null $juz
 * @property string|null $surat
 * @property string|null $ayat_dari
 * @property string|null $ayat_sampai
 * @property string $status
 * @property string|null $catatan
 * @property \Illuminate\Foundation\Support\Carbon|null $tanggal_setoran
 * @property int|null $nilai
 */
class StoreHafalanRequest extends FormRequest
{
    public function authorize()
    {
        // only ustadz users should be here; middleware handles it but double check
        return $this->user() && $this->user()->isUstadz();
    }

    public function rules()
    {
        return [
            'santri_id' => 'required|exists:santri,id',
            'juz' => 'nullable|integer|min:1|max:30',
            'surat' => 'nullable|string|max:255',
            'ayat_dari' => 'nullable|string|max:50',
            'ayat_sampai' => 'nullable|string|max:50',
            'status' => 'required|in:belum,sedang,selesai,mengulang',
            'catatan' => 'nullable|string',
            'tanggal_setoran' => 'nullable|date',
            'nilai' => 'nullable|integer|min:0|max:100',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'penulis' => 'required|string|max:100',
            'penerbit' => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:'.date('Y'),
            'isbn' => 'nullable|string|max:20',
            'stok' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi',
            'judul.max' => 'Maksimal judul buku 200 kata',
            'penulis.required' => 'Nama penulis wajib diisi',
            'penulis.max' => 'Maksimal nama penulis 100 kata',
            'penerbit.required' => 'Nama penerbit wajib diisi',
            'penerbit.max' => 'Maksimal nama penerbit 100 kata',
            'tahun_terbit.required' => 'Tahun buku diterbitkan wajib diisi',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka',
            'tahun_terbit.min' => 'Tahun terbit tidak valid',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun sekarang',
            'isbn.max' => 'ISBN maksimal 20 karakter.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}

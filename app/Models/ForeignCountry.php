<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForeignCountry extends Model
{
    protected $fillable = [
        'nome_italiano',
        'nome_inglese',
        'codice_catastale',
        'codice_iso_alpha2',
        'codice_iso_alpha3'
    ];

    /**
     * Find country by cadastral code
     *
     * @param string $code
     * @return ForeignCountry|null
     */
    public static function findByCodiceCatastale(string $code): ?ForeignCountry
    {
        return static::where('codice_catastale', strtoupper($code))->first();
    }

    /**
     * Search countries by name
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchByName(string $name)
    {
        return static::where('nome_italiano', 'LIKE', '%' . $name . '%')
            ->orWhere('nome_inglese', 'LIKE', '%' . $name . '%')
            ->get();
    }
}

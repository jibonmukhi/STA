<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItalianComune extends Model
{
    protected $table = 'italian_comuni';

    protected $fillable = [
        'nome',
        'regione',
        'provincia',
        'sigla_provincia',
        'codice_catastale'
    ];

    /**
     * Find comune by cadastral code
     *
     * @param string $code
     * @return ItalianComune|null
     */
    public static function findByCodiceCatastale(string $code): ?ItalianComune
    {
        return static::where('codice_catastale', strtoupper($code))->first();
    }

    /**
     * Search comuni by name
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchByName(string $name)
    {
        return static::where('nome', 'LIKE', '%' . $name . '%')->get();
    }
}

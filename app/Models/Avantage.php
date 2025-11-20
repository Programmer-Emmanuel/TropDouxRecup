<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Avantage extends Model
{
    public $incrementing = false; // empêche l'auto-incrémentation
    protected $keyType = 'string'; // la clé primaire sera une string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'nom_avantage'
    ];

   public function abonnements(){
        return $this->belongsToMany(
            Abonnement::class,
            'avantage_abonnement',
            'id_avantage',
            'id_abonnement'
        );
    }


}

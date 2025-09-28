<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Insumo;
use App\Models\ArchivoPedido;

class Pedido extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'fecha_entrega',
        'estado',
        'total',
    ];

    /**
     * Define la relación: un pedido pertenece a un cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Define la relación muchos a muchos con Insumo.
     */
    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('cantidad');
    }

    /**
     * Define la relación: un pedido tiene muchos archivos.
     */
    public function archivos()
    {
        return $this->hasMany(ArchivoPedido::class);
    }
}
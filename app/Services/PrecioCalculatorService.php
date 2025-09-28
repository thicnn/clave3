<?php

namespace App\Services;

use App\Models\Maquina;
use App\Models\Insumo;
use App\Models\Configuracion;

class PrecioCalculatorService
{
    /**
     * Calcular precio y costo para un ítem de pedido
     */
    public function calcularItem(array $data): array
    {
        $categoria = $data['categoria'];
        $cantidad = $data['cantidad'];
        $esDobleFaz = $data['es_doble_faz'] ?? false;
        $descuentoItem = $data['descuento_item'] ?? 0;

        // Obtener margen de ganancia desde configuración
        $margenGanancia = (float) Configuracion::obtener('margen_ganancia_default', 2.0);

        if ($categoria === 'servicio') {
            return $this->calcularServicio($data, $margenGanancia);
        } else {
            return $this->calcularImpresion($data, $margenGanancia);
        }
    }

    /**
     * Calcular para servicios (precio manual)
     */
    private function calcularServicio(array $data, float $margenGanancia): array
    {
        $precioManual = $data['precio_manual'] ?? 0;
        $descuentoItem = $data['descuento_item'] ?? 0;
        
        $montoDescuento = $precioManual * ($descuentoItem / 100);
        $precioVenta = $precioManual - $montoDescuento;
        
        return [
            'costo_produccion_item' => $precioManual / $margenGanancia, // Estimación del costo
            'precio_venta_item' => $precioVenta,
        ];
    }

    /**
     * Calcular para impresión/fotocopia
     */
    private function calcularImpresion(array $data, float $margenGanancia): array
    {
        $maquinaId = $data['maquina_id'];
        $insumoId = $data['insumo_id'];
        $tipoImpresion = $data['tipo_impresion'];
        $cantidad = $data['cantidad'];
        $esDobleFaz = $data['es_doble_faz'] ?? false;
        $descuentoItem = $data['descuento_item'] ?? 0;

        // Obtener máquina e insumo
        $maquina = Maquina::find($maquinaId);
        $insumo = Insumo::find($insumoId);

        if (!$maquina || !$insumo) {
            throw new \Exception('Máquina o insumo no encontrado');
        }

        // Calcular cantidad de hojas
        $cantidadHojas = $esDobleFaz ? (int) ceil($cantidad / 2) : $cantidad;

        // Calcular costos
        $costoPapel = $cantidadHojas * $insumo->costo_por_unidad;
        $costoImpresion = $cantidad * $maquina->getCostoImpresion($tipoImpresion);
        $costoProduccion = $costoPapel + $costoImpresion;

        // Calcular precio de venta
        $precioBase = $costoProduccion * $margenGanancia;
        $montoDescuento = $precioBase * ($descuentoItem / 100);
        $precioVenta = $precioBase - $montoDescuento;

        return [
            'costo_produccion_item' => $costoProduccion,
            'precio_venta_item' => $precioVenta,
            'cantidad_hojas' => $cantidadHojas,
        ];
    }

    /**
     * Validar datos para cálculo
     */
    public function validarDatos(array $data): array
    {
        $errores = [];

        if (!isset($data['categoria'])) {
            $errores[] = 'La categoría es requerida';
        }

        if (!isset($data['cantidad']) || $data['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }

        if ($data['categoria'] === 'servicio') {
            if (!isset($data['precio_manual']) || $data['precio_manual'] <= 0) {
                $errores[] = 'El precio manual es requerido para servicios';
            }
        } else {
            if (!isset($data['maquina_id'])) {
                $errores[] = 'La máquina es requerida para impresión/fotocopia';
            }
            if (!isset($data['insumo_id'])) {
                $errores[] = 'El insumo es requerido para impresión/fotocopia';
            }
            if (!isset($data['tipo_impresion'])) {
                $errores[] = 'El tipo de impresión es requerido';
            }
        }

        return $errores;
    }

    /**
     * Obtener opciones de máquinas según el tipo de impresión
     */
    public function getMaquinasDisponibles(string $tipoImpresion = null): array
    {
        $query = Maquina::activas();

        if ($tipoImpresion === 'color') {
            $query->whereNotNull('costo_color_carilla');
        }

        return $query->get()->map(function ($maquina) {
            return [
                'id' => $maquina->id,
                'nombre' => $maquina->nombre,
                'soporta_color' => $maquina->soportaColor(),
                'costo_bn' => $maquina->costo_bn_carilla,
                'costo_color' => $maquina->costo_color_carilla,
            ];
        })->toArray();
    }

    /**
     * Obtener insumos disponibles
     */
    public function getInsumosDisponibles(): array
    {
        return Insumo::where('stock_actual', '>', 0)
            ->get()
            ->map(function ($insumo) {
                return [
                    'id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'costo_por_unidad' => $insumo->costo_por_unidad,
                    'stock_actual' => $insumo->stock_actual,
                ];
            })->toArray();
    }
}
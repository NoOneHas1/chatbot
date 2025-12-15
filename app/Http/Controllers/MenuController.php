<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    /**
     * Obtener el menú principal (opciones sin parent_id)
     */
    public function root()
    {
        $items = MenuItem::whereNull('parent_id')
                         ->orderBy('order_index')
                         ->get();

        return response()->json([
            'type' => 'menu',
            'items' => $items,
            'menu_title' => "Menú principal" // → necesario para IA
        ]);
    }

    /**
     * Obtener submenús o la respuesta final
     */
    public function children($id)
    {
        $item = MenuItem::findOrFail($id);

        $children = MenuItem::where('parent_id', $id)
                            ->orderBy('order_index')
                            ->get();

        if ($children->count() > 0) {
            return response()->json([
                'type' => 'menu',
                'items' => $children,
                'menu_title' => $item->title, // → enviado a IA
                'parent_id' => $item->parent_id
            ]);
        }

        return response()->json([
            'type' => 'response',
            'text' => $item->response,
            'parent_id' => $item->parent_id
        ]);
    }
}

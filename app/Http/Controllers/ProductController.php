<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Producto A"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto"),
     *             @OA\Property(property="price", type="number", format="float", example="99.99"),
     *             @OA\Property(property="stock", type="integer", example="100")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producto creado"),
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validación de los datos de entrada
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
            ]);

            // Lógica para crear un producto
            $product = Product::create($validated);

            return response()->json($product, 201);  // Producto creado exitosamente
        } catch (\Exception $exception) {
            // Registrar el error en un archivo de log personalizado
            Log::channel('custom_error_log')->error('Error al crear producto: ' . $exception->getMessage());

            // Responder con el error y mensaje
            return response()->json([
                'error' => 'Error al crear el producto',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(null, 204);
    }
}

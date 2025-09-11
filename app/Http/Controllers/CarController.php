<?php

namespace App\Http\Controllers;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with('category');

        // البحث بالاسم أو الموديل أو الماركة
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('model', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // التصفية حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // التصفية حسب نطاق السعر
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('daily_rate', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        // التصفية حسب عدد المقاعد
        if ($request->has('seats') && !empty($request->seats)) {
            $query->where('seats', $request->seats);
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSortFields = ['id', 'model', 'brand', 'daily_rate', 'seats'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $cars = $query->get();

        return response()->json([
            'cars' => $cars,
            'total' => $cars->count(),
            'filters_applied' => [
                'search' => $request->get('search'),
                'category_id' => $request->get('category_id'),
                'min_price' => $request->get('min_price'),
                'max_price' => $request->get('max_price'),
                'seats' => $request->get('seats'),
                'fuel_type' => $request->get('fuel_type'),
                'transmission' => $request->get('transmission'),
                'has_ac' => $request->get('has_ac'),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]
        ]);
    }

    public function show($id)
    {
        $car = Car::with('category')->find($id);

        if (!$car) {
            return response()->json([
                'message' => 'Car not found.'
            ], 404);
        }

        return response()->json($car);
    }

}

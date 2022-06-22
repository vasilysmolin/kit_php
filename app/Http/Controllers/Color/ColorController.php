<?php

namespace App\Http\Controllers\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ad\AdIndexRequest;
use App\Models\CatalogAd;
use App\Models\Color;
use App\Objects\JsonHelper;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function __construct()
    {
    }

    public function index(AdIndexRequest $request): \Illuminate\Http\JsonResponse
    {

        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $name = $request->name;

        $builder = Color::when(!empty($id) && is_array($id), function ($query) use ($id) {
            $query->whereIn('id', $id);
        })
            ->when(!empty($name), function ($q) use ($name) {
                $q->where('name', 'ilike', "%{$name}%");
            });


        $color = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->get();
        $count = $builder->count();


        $data = (new JsonHelper())->getIndexStructure(new Color(), $color, $count, (int) $skip);

        return response()->json($data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $color = new Color();
        $color->fill($formData);
        $color->save();

        return response()->json([], 201, ['Location' => "/colors/$color->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $expand = $request->expand ? explode(',', $request->expand) : null;
        $color = Color::where('id', $id)
            ->when(!empty($expand), function ($q) use ($expand) {
                $q->with($expand);
            })
            ->first();

        abort_unless($color, 404);


        return response()->json($color);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        $color = Color::where('id', $id)
            ->first();
        $color->fill($formData);
        $color->update();

        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        Color::where('id', $id)->delete();
        return response()->json([], 204);
    }
}

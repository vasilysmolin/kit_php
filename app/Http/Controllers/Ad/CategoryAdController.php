<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\CatalogAdCategory;
use App\Objects\Files;
use App\Objects\JsonHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryAdController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $cabinet = isset($user) && $request->from === 'cabinet';
        $take = $request->take ?? config('settings.take_twenty_five');
        $skip = $request->skip ?? 0;
        $id = isset($request->id) ? explode(',', $request->id) : null;
        $files = resolve(Files::class);
        $builder = CatalogAdCategory::
            when(!empty($id) && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->whereNull('parent_id')
            ->where('active', 1);

        $category = $builder
            ->take((int) $take)
            ->skip((int) $skip)
            ->with('image', 'categories', 'categoriesParent', 'color')
            ->orderBy('id', 'ASC')
            ->get();

        $count = $builder->count();

        $category->each(function ($item) use ($files, $cabinet) {
            if (isset($item->image)) {
                $item->photo = $files->getFilePath($item->image);
                $item->makeHidden('image');
            }
            if ($cabinet) {
                $this->changeName($item->categories);
            }
        });

        $data = (new JsonHelper())->getIndexStructure(new CatalogAdCategory(), $category, $count, (int) $skip);

        return response()->json($data);
    }

    private function changeName($node)
    {
        if ($node->isEmpty()) {
            return;
        }
        $node->each(function ($item) {
            if ($item->name === 'Снять') {
                $item->name = 'Сдать';
            }
            if ($item->name === 'Купить') {
                $item->name = 'Продать';
            }
        });
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        if ((int) $formData['color_id'] === 0) {
            $formData['color_id'] = null;
        }
        $formData['profile_id'] = auth('api')->user()->profile->id;
        $formData['active'] = true;

        $formData['alias'] = Str::slug($formData['name'] . ' ' . str_random(5), '-');

        $category = new CatalogAdCategory();
        $category->fill($formData);

        $category->save();
        $files = resolve(Files::class);

        $files->save($category, $request['files']);

        return response()->json([], 201, ['Location' => "/category-declarations/$category->id"]);
    }

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $category = CatalogAdCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->with('image', 'categories', 'categoriesParent', 'color', 'filters.parameters')
            ->first();
        $files = resolve(Files::class);
        if (isset($category->image)) {
            $category->photo = $files->getFilePath($category->image);
        }

        abort_unless($category, 404);

        return response()->json($category);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $formData = $request->all();
        if ((int) $formData['color_id'] === 0) {
            $formData['color_id'] = null;
        }
        $category = CatalogAdCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->first();
        if (!isset($category)) {
            throw new ModelNotFoundException("Доступ запрещен", Response::HTTP_FORBIDDEN);
        }

        $category->fill($formData);

        $category->update();

        $files = resolve(Files::class);

        $files->save($category, $request['files']);


        return response()->json([], 204);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        CatalogAdCategory::where('alias', $id)
            ->when(ctype_digit($id), function ($q) use ($id) {
                $q->orWhere('id', (int) $id);
            })
            ->delete();
        return response()->json([], 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $pagination = 15;
        $podcasts = Podcast::with('episodes');
        try {
            Validator::make(\request()->all(), [
                'published' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1']
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Podcasts Fetched Successfully',
                'data' => $podcasts->paginate($pagination)
            ],200);
        }
        if (request()->has('published')) {
            $podcasts = $podcasts->where('published', '=', request()->input('published'));
        }
        return response()->json([
            'success' => true,
            'message' => 'Podcasts Fetched Successfully',
            'data' => $podcasts->paginate($pagination)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:125', 'unique:podcasts,title'],
                'about' => ['sometimes', 'nullable', 'string', 'max:' . config('variables.medium_text_size')],
                'published' => ['required', 'boolean'],
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input Data invalid',
                'data' => $e->errors()
            ],400);
        }

        $podcast = Podcast::create([
            'title' => strip_tags($request->input('title')),
            'about' => strip_tags($request->input('about')),
            'published' => $request->boolean('published'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Podcast Created Successfully',
            'data' => $podcast
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param Podcast $podcast
     * @return JsonResponse
     */
    public function show(Podcast $podcast): JsonResponse
    {
        if (!$podcast->published) {
            return response()->json([
                'success' => true,
                'message' => 'Requested Podcast Unavailable now',
                'data' => null
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Requested Podcast Returned Successfully',
            'data' => $podcast
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Podcast $podcast
     * @return JsonResponse
     */
    public function update(Request $request, Podcast $podcast): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:125', Rule::unique('podcasts', 'title')->ignore($podcast->id)],
                'about' => ['sometimes', 'nullable', 'string', 'max:' . config('variables.medium_text_size')],
                'published' => ['required', 'boolean'],
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input Data invalid',
                'data' => $e->errors()
            ],400);
        }

        $podcast->update([
            'title' => strip_tags($request->input('title')),
            'about' => strip_tags($request->input('about')),
            'published' => $request->boolean('published'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Podcast Updated Successfully',
            'data' => $podcast
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Podcast $podcast
     * @return JsonResponse
     */
    public function destroy(Podcast $podcast): JsonResponse
    {
        DB::beginTransaction();
        if (!is_numeric($podcast->episodes()->delete())) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong - Error Code: 324223',
                'data' => $podcast
            ], 400);
        }

        if (!is_bool($podcast->delete())) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong - Error Code: 432542',
                'data' => $podcast
            ], 400);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Podcast Deleted Successfully',
            'data' => null
        ]);
    }
}

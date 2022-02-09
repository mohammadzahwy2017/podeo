<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $pagination = 15;
        $episodes = Episode::with('podcast');
        try {
            Validator::make(\request()->all(), [
                'published' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1'],
                'podcast_id' => ['sometimes', 'nullable', 'digits_between:1,10', 'gt:0', 'exists:podcasts,id'],
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Episodes Fetched Successfully',
                'data' => $episodes->paginate($pagination)
            ]);
        }
        if (request()->has('published')) {
            $episodes = $episodes->where('published', '=', request()->input('published'));
        }
        if (request()->has('podcast_id')) {
            $episodes = $episodes->where('podcast_id', '=', request()->input('podcast_id'));
        }
        return response()->json([
            'success' => true,
            'message' => 'Episodes Fetched Successfully',
            'data' => $episodes->paginate($pagination)
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
                'podcast_id' => ['required', 'digits_between:1,10', 'gt:0', 'exists:podcasts,id'],
                'title' => ['required', 'string', 'max:125', Rule::unique('episodes', 'title')->where('podcast_id', $request->input('podcast_id'))],
                'description' => ['sometimes', 'nullable', 'string', 'max:' . config('variables.medium_text_size')],
                'duration' => ['required', 'numeric'],
                'published' => ['required', 'boolean'],
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input Data invalid',
                'data' => $e->errors()
            ]);
        }
        $episode = Episode::create([
            'title' => strip_tags($request->input('title')),
            'description' => strip_tags($request->input('description')),
            'duration' => $request->input('duration'),
            'podcast_id' => $request->input('podcast_id'),
            'published' => $request->boolean('published'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Episode Created Successfully',
            'data' => $episode
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Episode $episode
     * @return JsonResponse
     */
    public function show(Episode $episode): JsonResponse
    {
        if (!$episode->published) {
            return response()->json([
                'success' => true,
                'message' => 'Requested Episode Unavailable now',
                'data' => null
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Requested Episode Returned Successfully',
            'data' => $episode
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Episode $episode
     * @return JsonResponse
     */
    public function update(Request $request, Episode $episode): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'podcast_id' => ['required', 'digits_between:1,10', 'gt:0', 'exists:podcasts,id'],
                'title' => ['required', 'string', 'max:125', Rule::unique('episodes', 'title')->where('podcast_id', $request->input('podcast_id'))->ignore($episode->id)],
                'description' => ['sometimes', 'nullable', 'string', 'max:' . config('variables.medium_text_size')],
                'duration' => ['required', 'numeric'],
                'published' => ['required', 'boolean'],
            ])->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input Data invalid',
                'data' => $e->errors()
            ]);
        }
        $episode->update([
            'title' => strip_tags($request->input('title')),
            'description' => strip_tags($request->input('description')),
            'duration' => $request->input('duration'),
            'podcast_id' => $request->input('podcast_id'),
            'published' => $request->boolean('published'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Episode Updated Successfully',
            'data' => $episode
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Episode $episode
     * @return JsonResponse
     */
    public function destroy(Episode $episode): JsonResponse
    {
        if (!is_bool($episode->delete())) {
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong - Error Code: 321465',
                'data' => $episode
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'Episode Deleted Successfully',
            'data' => null
        ]);
    }
}

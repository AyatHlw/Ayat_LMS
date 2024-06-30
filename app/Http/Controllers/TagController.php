<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Course;
use App\Models\CourseTag;
use App\Models\Tag;
use App\Services\Course\CourseService;
use App\Services\TagService;
use http\Env\Response;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;

    }

    public function createTag(Request $request)
    {
        $request->validate([
            'tag_name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
        ]);

        try {
            $tag = $this->tagService->createTag(
                $request->input('tag_name'),
                $request->input('category_name')
            );

            return response()->json(['message' => 'Tag created successfully', 'tag' => $tag], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function addTagsToCourse(Request $request, $course_id)
    {
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        try {
            $tags = $this->tagService->addTagsToCourse($course_id, $request->input('tag_ids'));
            return TagResource::collection($tags);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteTag($tagId)
    {
        try {
            $result = $this->tagService->deleteTag($tagId);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateTag($tagId,Request $request)
    {
        try {
            $tag = $this->tagService->updateTag($tagId,$request);
            return response()->json(['message' => 'Tag updated successfully', 'tag' => $tag], 200);
        } catch (Exception $e) {
            return response()->json([
               'message' => $e->getMessage()
            ],500);
        }
    }

    public function getCourseTags($course_id)
    {
        try {
            $tags = $this->tagService->getCourseTags($course_id);
            return TagResource::collection($tags);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getTagsByCategory($category_id)
    {
        try {
            $tags = $this->tagService->getTagsByCategory($category_id);
            return TagResource::collection($tags);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

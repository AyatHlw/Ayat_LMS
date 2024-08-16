<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Course;
use App\Models\CourseTag;
use App\Models\Tag;
use App\Services\Course\CourseService;
use App\Services\TagService;
use App\Http\Responses\Response;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use Twilio\Rest\Taskrouter\V1\Workspace\Task\ReservationOptions;

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
            'categoryId' => 'required|exists:categories,id',
        ]);

        try {
            $tag = $this->tagService->createTag(
                $request->input('tag_name'),
                $request->input('categoryId'),
            );

            return Response::success(__('messages.tag_created'), TagResource::make($tag));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
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
            return Response::success(__('messages.tags_retrieved'), TagResource::collection($tags));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function deleteTag($tagId)
    {
        try {
            $result = $this->tagService->deleteTag($tagId);
            return Response::success($result['message']);
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function updateTag($tagId, Request $request)
    {
        try {
            $tag = $this->tagService->updateTag($tagId, $request);
            return Response::success(__('messages.tag_updated'), TagResource::make($tag));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function getAllTags()
    {
        try {
            $tags = $this->tagService->getAllTags();
            return Response::success(__('messages.tags_retrieved'), TagResource::collection($tags));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function getCourseTags($course_id)
    {
        try {
            $tags = $this->tagService->getCourseTags($course_id);
            return Response::success(__('messages.tags_retrieved'), TagResource::collection($tags));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }


    public function getTagsByCategory($category_id)
    {
        try {
            $tags = $this->tagService->getTagsByCategory($category_id);
            return Response::success(__('messages.tags_retrieved'), TagResource::collection($tags));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

}

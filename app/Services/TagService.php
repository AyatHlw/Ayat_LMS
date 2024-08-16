<?php

namespace App\Services;

use App\Http\Responses\Response;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use function Laravel\Prompts\table;
use function PHPUnit\Framework\isNull;

class TagService
{

    /*
    public function createTag(string $tagName, string $categoryName): Tag
    {
        try {
            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                throw new \Exception("Category not found");
            }

            $tag = Tag::create([
                'name' => $tagName,
                'category_id' => $category->id,
            ]);

            return $tag;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    */

    public function createTag(string $tagName, $categoryId): Tag
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) throw new \Exception(__('messages.category_not_found'));
            $tag = Tag::create([
                'name' => $tagName,
                'category_id' => $category->id,
            ]);

            return $tag;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(__('messages.failed_to_create_tag', ['error' => $e->getMessage()]));
        }
    }

    public function addTagsToCourse($course_id, $tag_ids)
    {
        try {
            $course = Course::find($course_id);
            if (!$course) throw new \Exception(__('messages.course_not_found'));
            $course->tags()->attach($tag_ids);
            return $course->tags;
        } catch (\Exception $e) {
            throw new \Exception(__('messages.failed_to_add_tags', ['error' => $e->getMessage()]));
        }
    }

    public function deleteTag($tagId): array
    {
        try {
            $tag = Tag::find($tagId);
            if (!$tag) throw new \Exception(__('messages.tag_not_found'), 404);
            $tag->delete();

            DB::commit();

            return ['message' => __('messages.tag_deleted')];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(__('messages.failed_to_add_tags', ['error' => $e->getMessage()]));
        }
    }

    public function updateTag($quizId, Request $request)
    {
        try {
            $tag = Tag::find($quizId);
            if (!$tag) throw new \Exception(__('messages.tag_not_found'), 404);
            $category = Category::find($request['categoryId']);
            if (!$category) throw new \Exception(__('messages.category_not_found'));

            if (isset($request['name'])) {
                $tag->name = $request['name'];
                $tag->save();
            }
            if (isset($request['categoryId'])) {
                $tag->category_id = $category->id;
                $tag->save();
            }
            return $tag;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(__('messages.failed_to_update_tag', ['error' => $e->getMessage()]));
        }
    }

    public function getAllTags()
    {
        $tags = Tag::query()->get();
        if(!$tags) throw new \Exception(__('messages.no_tags'));
        return $tags;
    }

    public function getTagsByCategory($category_id)
    {
        $category = Category::with('tags')->find($category_id);
        if (!$category) throw new \Exception(__('messages.category_not_found'), 404);
        if (is_null($category->tags)) throw new \Exception(__('messages.no_tags'), 200);
        return $category->tags;
    }

    public function getCourseTags($course_id)
    {
        $course = Course::query()->firstWhere('id', $course_id);
        if (!$course) throw new \Exception(__('messages.course_not_found'), 404);
        if (!($course->tags)) throw new \Exception(__('messages.no_tags'), 404);
        return $course->tags;
    }
}

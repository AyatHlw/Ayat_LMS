<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class TagService
{
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

    public function addTagsToCourse($course_id, $tag_ids)
    {
        try {
            $course = Course::findOrFail($course_id);
            $course->tags()->attach($tag_ids);
            return $course->tags;
        } catch (\Exception $e) {
            throw new \Exception("Failed to add tags: " . $e->getMessage());
        }
    }

    public function deleteTag($tagId): array
    {
        try {
            $tag = Tag::findOrFail($tagId);
            $tag->delete();
            DB::commit();

            return ['message' => 'tag deleted successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTag($quizId, Request $request)
    {
        try {
            $tag = Tag::findOrFail($quizId);
            $category = Category::where('name', $request['category_name'])->first();
            if (isset($request['name'])) {
                $tag->name = $request['name'];
                $tag->save();
            }
            if (isset($request['category_name'])) {
                $tag->category_id = $category->id;
                $tag->save();
            }
            return $tag;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getTagsByCategory($category_id)
    {
        try {
            $category = Category::with('tags')->findOrFail($category_id);
            return $category->tags;
        } catch (\Exception $e) {
            throw new \Exception('Error fetching tags for category: ' . $e->getMessage());
        }
    }

    public function getCourseTags($course_id)
    {
        try {
            $course = Course::with('tags')->findOrFail($course_id);
            return $course->tags;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch tags: " . $e->getMessage());
        }
    }
}
